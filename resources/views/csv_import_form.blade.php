@extends('layouts.app')

@section('title', 'Import Files Page')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card text-center mb-4">
                    <div class="card-body bg-silver">
                        <form action="{{ route('import') }}" method="POST" enctype="multipart/form-data"
                            class="d-flex justify-content-center align-items-center">
                            <div class="mx-5">
                                <h4 class="mb-4">Drag and drop your files</h4>
                                <p class="text-muted mb-4">File formats we support <i class="fas fa-info-circle"></i></p>
                            </div>
                            @csrf
                            <div>
                                <input type="file" name="csv_file" required id="fileInput" class="d-none">
                                <label for="fileInput" class="btn bg-educ color-white">Import Manually</label>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card d-none" id="file-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-end align-items-center">
                            <div class="col-sm-4">
                                <img src="../images/csv.png" alt="" style="height:4rem">
                                <p id="file-name" class="text-muted d-none"></p>
                            </div>
                            <div class="col-sm-4">
                                <div class="progress" role="progressbar" aria-valuenow="0" aria-valuemin="0"
                                    aria-valuemax="100">
                                    <div class="progress-bar bg-purple" id="progressBar" style="width: 0%;"></div>
                                </div>
                            </div>
                            <div class="col-sm-4 ">
                                <button type="submit" id="submit-btn" class="btn bg-educ color-white d-none"
                                    style="margin-left: auto">Submit</button>
                            </div>
                        </div>

                        <p id="progress-message" class="text-muted d-none mt-2"></p>
                        <p id="error-message" class="text-danger d-none mt-2"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        const fileInput = document.getElementById('fileInput');
        const submitBtn = document.getElementById('submit-btn');
        const fileName = document.getElementById('file-name');
        const card = document.getElementById('file-card');
        const progressBar = document.getElementById('progressBar');
        const progressMessage = document.getElementById('progress-message');
        const errorMessage = document.getElementById('error-message');

        fileInput.addEventListener('change', () => {
            const fileNameText = fileInput.files[0].name;
            fileName.textContent = `${fileNameText}`;
            fileName.classList.remove('d-none');
            submitBtn.classList.remove('d-none');
            card.classList.remove('d-none');
        });

        submitBtn.addEventListener('click', (e) => {
            e.preventDefault();
            const formData = new FormData();
            formData.append('csv_file', fileInput.files[0]);
            progressBar.style.width = '0%';
            progressMessage.textContent = 'Uploading...';
            progressMessage.classList.remove('d-none');
            errorMessage.classList.add('d-none'); // Hide any previous error message

            fetch('{{ route('import') }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        if (response.status === 500) {
                            throw new Error('Internal Server Error: Please try again later.');
                        }
                        throw new Error('Upload failed');
                    }
                    // Check if the response is a CSV file
                    const contentDisposition = response.headers.get('Content-Disposition');
                    if (contentDisposition && contentDisposition.includes('attachment')) {
                        return response.blob();
                    } else {
                        return response.json();
                    }
                })
                .then(data => {
                    if (data instanceof Blob) {
                        // Show the download prompt
                        showDownloadPrompt(data);
                        progressMessage.classList.add('d-none');
                        progressBar.classList.add('d-none');

                    } else if (data.success) {
                        progressBar.style.width = '100%';
                        progressMessage.textContent = 'Upload complete!';
                    } else {
                        throw new Error(data.message || 'Upload failed');
                    }
                })
                .catch(error => {
                    progressBar.style.width = '0%';
                    progressMessage.classList.add('d-none');
                    errorMessage.textContent = error.message;
                    errorMessage.classList.remove('d-none');
                });
        });

        function showDownloadPrompt(blobData) {
            // Create the modal element
            const downloadPrompt = document.createElement('div');
            downloadPrompt.style.position = 'fixed';
            downloadPrompt.style.top = '50%';
            downloadPrompt.style.left = '50%';
            downloadPrompt.style.transform = 'translate(-50%, -50%)';
            downloadPrompt.style.backgroundColor = '#fff';
            downloadPrompt.style.padding = '20px';
            downloadPrompt.style.boxShadow = '0px 0px 10px rgba(0, 0, 0, 0.1)';
            downloadPrompt.style.zIndex = '1000';
            downloadPrompt.innerHTML = `
        <h5>Invalid Rows Found</h5>
        <p>We found some invalid rows in your file. Would you like to download them as a CSV?</p>
        <button id="download-btn" class="btn btn-primary">Download</button>
        <button id="cancel-btn" class="btn btn-secondary">Cancel</button>
    `;

            // Append the modal to the body
            document.body.appendChild(downloadPrompt);

            // Handle download button click
            document.getElementById('download-btn').addEventListener('click', () => {
                const url = window.URL.createObjectURL(blobData);
                const a = document.createElement('a');
                a.href = url;
                a.download = 'invalid_emails.csv'; // Set the file name
                document.body.appendChild(a);
                a.click();
                a.remove();
                window.URL.revokeObjectURL(url);

                // Remove the modal after download
                downloadPrompt.remove();
            });

            // Handle cancel button click
            document.getElementById('cancel-btn').addEventListener('click', () => {
                downloadPrompt.remove();
            });
        }
    </script>
@endsection
