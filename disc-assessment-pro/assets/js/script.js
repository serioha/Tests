document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('import-json').addEventListener('click', function() {
        const input = document.createElement('input');
        input.type = 'file';
        input.accept = '.json';
        input.onchange = e => {
            const file = e.target.files[0];
            const formData = new FormData();
            formData.append('json_file', file);
            formData.append('action', 'import_json_file');

            fetch('admin-post.php', {
                method: 'POST',
                body: formData,
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                location.reload(); // Reload the page to see the changes
            })
            .catch(error => {
                console.error('Error:', error);
            });
        };
        input.click();
    });
});
