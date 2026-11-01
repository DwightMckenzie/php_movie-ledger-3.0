function exportCsv() {

	console.log('export js');

    const xhr = new XMLHttpRequest();
    xhr.open('POST', './ajax/movieExportCsv-1.0.php', true);
    xhr.responseType = 'blob'; // <-- tell the browser to expect binary/file data

    xhr.onload = function() {
        if (xhr.status === 200) {
            // Create a temporary URL pointing to the blob
            const blob = xhr.response;
            const downloadUrl = window.URL.createObjectURL(blob);

            // Create a hidden link and click it to trigger the download
            const a = document.createElement('a');
            a.href = downloadUrl;
            a.download = 'movie_ledger_export.csv'; // filename for the saved file
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);

            // Clean up the temporary URL
            window.URL.revokeObjectURL(downloadUrl);
        } else {
            console.error('Error:', xhr.status);
        }
    };

    xhr.send();
	
}