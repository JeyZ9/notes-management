<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Notes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .navbar {
            box-shadow: 0 2px 4px rgba(0,0,0,.1);
        }
        .card {
            border: none;
            box-shadow: 0 4px 8px rgba(0,0,0,.1);
            transition: transform .2s;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .note-card .card-body {
            padding: 1.5rem;
        }
        .note-card .card-title {
            font-weight: bold;
            margin-bottom: 1rem;
        }
        .note-card .card-text {
            color: #555;
            white-space: pre-wrap; /* Respects newlines in content */
        }
        .note-actions {
            position: absolute;
            top: 1rem;
            right: 1rem;
            display: flex;
            gap: .5rem;
        }
        .note-actions .btn {
            width: 30px;
            height: 30px;
            padding: 0;
            line-height: 30px;
            border-radius: 50%;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <i class="fas fa-book-open"></i>
                MyNotes
            </a>
            <button id="logout-button" class="btn btn-outline-secondary">
                <i class="fas fa-sign-out-alt"></i> Logout
            </button>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="row">
            <!-- Add Note Form -->
            <div class="col-lg-4 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-4"><i class="fas fa-plus-circle"></i> Add a New Note</h4>
                        <form id="add-note-form">
                            <div class="mb-3">
                                <label for="title" class="form-label">Title</label>
                                <input type="text" class="form-control" id="title" placeholder="Enter note title" required>
                            </div>
                            <div class="mb-3">
                                <label for="content" class="form-label">Content</label>
                                <textarea class="form-control" id="content" rows="5" placeholder="What's on your mind?" required></textarea>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Add Note</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Notes List -->
            <div class="col-lg-8">
                <h2 class="mb-4">Your Notes</h2>
                <div id="note-list" class="row">
                    <!-- Notes will be dynamically inserted here -->
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Note Modal -->
    <div class="modal fade" id="editNoteModal" tabindex="-1" aria-labelledby="editNoteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editNoteModalLabel">Edit Note</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="edit-note-form">
                        <input type="hidden" id="edit-note-id">
                        <div class="mb-3">
                            <label for="edit-title" class="form-label">Title</label>
                            <input type="text" class="form-control" id="edit-title" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit-content" class="form-label">Content</label>
                            <textarea class="form-control" id="edit-content" rows="5" required></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="save-changes-button">Save changes</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const apiToken = localStorage.getItem('api_token');
        let editNoteModal; // To hold the modal instance

        document.addEventListener('DOMContentLoaded', function () {
            editNoteModal = new bootstrap.Modal(document.getElementById('editNoteModal'));
        });

        if (!apiToken) {
            window.location.href = '/login';
        }

        async function getNotes() {
            const noteList = document.getElementById('note-list');
            noteList.innerHTML = '<p>Loading notes...</p>';

            try {
                const response = await fetch('/api/notes', {
                    headers: {
                        'Authorization': `Bearer ${apiToken}`,
                        'Accept': 'application/json',
                    }
                });

                if (!response.ok) {
                    if (response.status === 401) {
                        localStorage.removeItem('api_token');
                        window.location.href = '/login';
                    } else {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return;
                }

                const result = await response.json();
                const notes = result.data;
                noteList.innerHTML = '';

                if (notes.length === 0) {
                    noteList.innerHTML = '<p class="text-muted">You haven\'t created any notes yet. Add one using the form!</p>';
                } else {
                    notes.forEach(note => {
                        const noteCol = document.createElement('div');
                        noteCol.className = 'col-md-6 mb-4';
                        noteCol.innerHTML = `
                            <div class="card note-card h-100">
                                <div class="card-body">
                                    <div class="note-actions">
                                        <button class="btn btn-info btn-sm" onclick='openEditModal(${note.id}, "${escapeString(note.title)}", "${escapeString(note.content)}")'>
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-danger btn-sm" onclick="deleteNote(${note.id})">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                    <h5 class="card-title">${note.title}</h5>
                                    <p class="card-text">${note.content}</p>
                                </div>
                            </div>
                        `;
                        noteList.appendChild(noteCol);
                    });
                }
            } catch (error) {
                console.error('Error fetching notes:', error);
                noteList.innerHTML = '<p class="text-danger">Failed to load notes.</p>';
            }
        }

        document.getElementById('add-note-form').addEventListener('submit', async function(event) {
            event.preventDefault();
            const title = document.getElementById('title').value;
            const content = document.getElementById('content').value;

            try {
                const response = await fetch('/api/notes', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${apiToken}`,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ title, content })
                });

                if (!response.ok) {
                     if (response.status === 401) {
                        alert('Authentication error. Redirecting to login.');
                        localStorage.removeItem('api_token');
                        window.location.href = '/login';
                    } else {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return;
                }
                this.reset();
                getNotes();
            } catch (error) {
                console.error('Error adding note:', error);
                alert('Failed to add note.');
            }
        });

        function openEditModal(id, title, content) {
            document.getElementById('edit-note-id').value = id;
            document.getElementById('edit-title').value = unescapeString(title);
            document.getElementById('edit-content').value = unescapeString(content);
            editNoteModal.show();
        }

        document.getElementById('save-changes-button').addEventListener('click', async function() {
            const id = document.getElementById('edit-note-id').value;
            const title = document.getElementById('edit-title').value;
            const content = document.getElementById('edit-content').value;

            try {
                const response = await fetch(`/api/notes/${id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${apiToken}`,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ title, content })
                });

                if (!response.ok) {
                    if (response.status === 401) {
                        alert('Authentication error. Redirecting to login.');
                        localStorage.removeItem('api_token');
                        window.location.href = '/login';
                    } else {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return;
                }
                
                editNoteModal.hide();
                getNotes();
            } catch (error) {
                console.error('Error updating note:', error);
                alert('Failed to update note.');
            }
        });

        async function deleteNote(id) {
            if (!confirm('Are you sure you want to delete this note?')) {
                return;
            }

            try {
                const response = await fetch(`/api/notes/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Authorization': `Bearer ${apiToken}`,
                        'Accept': 'application/json',
                    }
                });

                if (!response.ok) {
                    if (response.status === 401) {
                        alert('Authentication error. Redirecting to login.');
                        localStorage.removeItem('api_token');
                        window.location.href = '/login';
                    } else {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return;
                }
                getNotes();
            } catch (error) {
                console.error('Error deleting note:', error);
                alert('Failed to delete note.');
            }
        }

        document.getElementById('logout-button').addEventListener('click', async () => {
            try {
                 await fetch('/api/logout', {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${apiToken}`,
                        'Accept': 'application/json',
                    }
                });
            } catch(e) {
                console.error("Logout failed, but clearing token anyway.", e);
            } finally {
                localStorage.removeItem('api_token');
                window.location.href = '/login';
            }
        });

        function escapeString(str) {
            return str.replace(/"/g, '&quot;').replace(/'/g, '&#39;').replace(/\n/g, '\\n');
        }

        function unescapeString(str) {
            return str.replace(/&quot;/g, '"').replace(/&#39;/g, "'").replace(/\\n/g, '\n');
        }

        if(apiToken) {
            getNotes();
        }
    </script>
</body>
</html>