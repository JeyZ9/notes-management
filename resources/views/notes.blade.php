<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notes API</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>My Notes</h1>
            <button id="logout-button" class="btn btn-secondary">Logout</button>
        </div>

        <!-- Form for adding a new note -->
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Add a New Note</h5>
                <form id="add-note-form">
                    <div class="mb-3">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" class="form-control" id="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="content" class="form-label">Content</label>
                        <textarea class="form-control" id="content" rows="3" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Add Note</button>
                </form>
            </div>
        </div>

        <!-- Display Notes -->
        <h2>Note List</h2>
        <div id="note-list" class="row">
            <!-- Notes will be dynamically inserted here -->
        </div>
    </div>

    <script>
        const apiToken = localStorage.getItem('api_token');

        // If no token, redirect to login
        if (!apiToken) {
            window.location.href = '/login';
        }

        // Function to fetch and display notes
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
                        // Token might be expired/invalid, clear it and redirect
                        localStorage.removeItem('api_token');
                        window.location.href = '/login';
                    } else {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return;
                }

                const notes = await response.json();
                noteList.innerHTML = ''; // Clear loading message

                if (notes.data.length === 0) {
                    noteList.innerHTML = '<p>No notes found.</p>';
                } else {
                    notes.data.forEach(note => {
                        const noteCol = document.createElement('div');
                        noteCol.className = 'col-md-4 mb-3';
                        noteCol.innerHTML = `
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">${note.title}</h5>
                                    <p class="card-text">${note.content}</p>
                                    <button class="btn btn-danger btn-sm" onclick="deleteNote(${note.id})">Delete</button>
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

        // Function to add a new note
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

                // Clear form and refresh notes
                this.reset();
                getNotes();
            } catch (error) {
                console.error('Error adding note:', error);
                alert('Failed to add note.');
            }
        });

        // Function to delete a note
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
                
                getNotes(); // Refresh the list
            } catch (error) {
                console.error('Error deleting note:', error);
                alert('Failed to delete note.');
            }
        }

        // Logout functionality
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

        // Initial load
        if(apiToken) {
            getNotes();
        }
    </script>
</body>
</html>
