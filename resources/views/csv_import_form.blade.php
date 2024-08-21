@extends('layouts.app')

@section('title', 'Import Files Page')

@section('content')
    <style>
        /* Custom styling for the select box */
        select {
            appearance: none;
            background-color: transparent;
            padding: 0.5rem;
            border: 1px solid #c58ca8;
            /* Similar color to the one in the image */
            border-radius: 50px;
            outline: none;
            color: #660634;
            /* Text color similar to the border */
            font-size: 1rem;
            background-image: url('data:image/svg+xml;charset=UTF-8,%3csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 24 24\' width=\'24\' height=\'24\' fill=\'%23c58ca8\'%3e%3cpath d=\'M7.4 8.8l3.6 3.6 3.6-3.6 1.4 1.4-5 5-5-5z\'/%3e%3c/svg%3e');
            /* Dropdown arrow */
            background-repeat: no-repeat;
            background-position: right 0.5rem center;
            background-size: 1rem;
        }

        select:focus {
            border-color: #c58ca8;
            box-shadow: 0 0 0 2px rgba(197, 140, 168, 0.5);
            /* Add shadow similar to focus state */
        }

        .card-custom {
            border: 1px solid #c58ca8;
            border-radius: 0.5rem;
        }

        .progress-bar-custom {
            background-color: #870f4a;
            /* Custom progress bar color */
        }

        .btn-custom {
            background-color: #870f4a;
            color: white;
            border: none;
        }

        .btn-close-custom {
            color: #c58ca8;
        }

        .status-text {
            color: green;
        }

        .drop-zone {
            border: 2px dashed #c58ca8;
            border-radius: 0.5rem;
            padding: 1.5rem;
            text-align: center;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .drop-zone.dragover {
            background-color: #f2e6ec;
        }
    </style>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-sm-12">
                <label for="platform" class="block text-gray-700 font-bold mb-2">Select platform</label>
                <div class=" w-100 mb-4" style="width: 100%">
                    <select id="platform" class=" w-100">
                        <option value="linkedin">LinkedIn</option>
                        <option value="facebook">Facebook</option>
                        <option value="twitter">Twitter</option>
                        <option value="instagram">Instagram</option>
                    </select>
                </div>

                <div class="card text-center mb-4">
                    <div class="card-body bg-silver" id="dropZone">
                        <form action="{{ route('import') }}" method="POST" enctype="multipart/form-data"
                            class="d-flex justify-content-center align-items-center">
                            <div class="mx-5">
                                <h4 class="mb-4">Drag and drop your files</h4>
                                <p class="text-muted mb-4" title="Only .csv is suppoted">File formats we support <i
                                        class="fas fa-info-circle"></i></p>
                            </div>
                            @csrf
                            <div>
                                <input accept=".csv" type="file" name="csv_file" required id="fileInput" class="d-none">

                            </div>
                            <div>
                                <label for="fileInput" class="btn bg-educ color-white mt-4">Import Manually</label>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card d-none" id="file-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <img src="../images/csv.png" alt="" style="height:4rem">
                                <p id="file-name" class="text-muted d-none"></p>
                            </div>
                            <div class="w-50">
                                <div class="progress" id="progressContainer" role="progressbar" aria-valuenow="0"
                                    aria-valuemin="0" aria-valuemax="100">
                                    <div class="progress-bar bg-educ" id="progressBar" style="width: 0%;"></div>
                                </div>
                                <p id="progress-message" class="text-muted d-none mt-2"></p>
                                
                                <p id="error-message" class="text-danger d-none mt-2"></p>
                            </div>
                            <div>
                                <button type="submit" id="submit-btn" class="btn bg-educ color-white d-none"
                                    style="margin-left: auto">Submit</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        const dropZone = document.getElementById('dropZone');
        const fileInput = document.getElementById('fileInput');
        const uploadForm = document.getElementById('uploadForm');

        const submitBtn = document.getElementById('submit-btn');
        const fileName = document.getElementById('file-name');
        const card = document.getElementById('file-card');
        const progressContainer = document.getElementById('progressContainer');
        const progressBar = document.getElementById('progressBar');
        const progressMessage = document.getElementById('progress-message');
        const errorMessage = document.getElementById('error-message');



        dropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropZone.classList.add('dragover');
        });


        dropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropZone.classList.remove('dragover');
            const files = e.dataTransfer.files;
            if (files.length) {
                fileInput.files = files;
                handleFiles();
            }
        });

        fileInput.addEventListener('change', handleFiles);

        function handleFiles() {
            const fileNameText = fileInput.files[0].name;
            fileName.textContent = `${fileNameText}`;
            fileName.classList.remove('d-none');
            submitBtn.classList.remove('d-none');
            card.classList.remove('d-none');
            errorMessage.classList.add('d-none');
            progressContainer.classList.add('d-none');
            progressMessage.classList.add('d-none');
        }

        submitBtn.addEventListener('click', (e) => {
            e.preventDefault();
            submitBtn.classList.add('d-none');
            progressContainer.classList.remove('d-none')
            const formData = new FormData();
            formData.append('csv_file', fileInput.files[0]);
            progressBar.style.width = '0%';
            progressMessage.textContent = 'Uploading...';
            progressMessage.classList.remove('d-none');
            errorMessage.classList.add('d-none'); // Hide any previous error message
            progressBar.style.width = '30%';

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
                        } else if (error.status === 422) {
                            const errors = error.errors;
                            let errorText = 'Validation failed for the uploaded file:<br>';
                            for (const [key, errorMessages] of Object.entries(errors)) {
                                errorText += errorMessages.join('<br>') + '<br>';
                            }
                            errorMessage.innerHTML = errorText;
                        } else {
                            errorMessage.textContent = error.message;
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
                        progressContainer.classList.add('d-none');
                        progressBar.classList.add('d-none');
                        card.classList.add('d-none')

                    } else if (data.success) {

                        progressBar.style.width = '100%';
                        progressMessage.textContent = 'Upload complete!';


                        let valid_count = data.data.valid_count;
                        let invalid_count = data.data.invalid_count;
                        let duplicate_count = data.data.duplicate_count;
                        let total_count = valid_count + invalid_count + duplicate_count;


                        if (data.data.invalid_count > 0) {
                            let download_invalid_link = data.data.download_invalid_link;
                            showDownloadPrompt(valid_count, invalid_count, duplicate_count, total_count,
                                download_invalid_link);

                        } else {
                            showResult(valid_count, invalid_count, duplicate_count, total_count);
                        }


                        progressMessage.classList.remove('d-none');
                        progressContainer.classList.add('d-none');
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

        function showResult(valid_count, invalid_count, duplicate_count, total_count) {
            const result = document.createElement('div');
            result.style.position = 'fixed';
            result.style.top = '50%';
            result.style.left = '50%';
            result.style.transform = 'translate(-50%, -50%)';
            result.style.backgroundColor = '#fff';
            result.style.padding = '20px';
            result.style.boxShadow = '0px 0px 10px rgba(0, 0, 0, 0.4)';
            result.style.zIndex = '1000';

            result.innerHTML = `
        <h5>Upload complete!</h5>
        <ul>
            <li>Total Rows: ${total_count}</li>
            <li>Imported Rows: ${valid_count}</li> 
            <li>Invalid Rows: ${invalid_count}</li> 
            <li>Duplicate Rows: ${duplicate_count}</li>    
        </ul>
        <button id="cancel-btn" class="btn bg-educ color-white">Close</button>
    `;
            // Append the modal to the body
            document.body.appendChild(result);
            // Handle cancel button click
            document.getElementById('cancel-btn').addEventListener('click', () => {
                result.remove();
            });
        }

        //function showDownloadPrompt(blobData) {
        function showDownloadPrompt(valid_count, invalid_count, duplicate_count, total_count, download_invalid_link) {
            // Create the modal element
            const downloadPrompt = document.createElement('div');
            downloadPrompt.style.position = 'fixed';
            downloadPrompt.style.top = '50%';
            downloadPrompt.style.left = '50%';
            downloadPrompt.style.transform = 'translate(-50%, -50%)';
            downloadPrompt.style.backgroundColor = '#fff';
            downloadPrompt.style.padding = '20px';
            downloadPrompt.style.boxShadow = '0px 0px 10px rgba(0, 0, 0, 0.4)';
            downloadPrompt.style.zIndex = '1000';

            downloadPrompt.innerHTML = `
        
        <ul>
            <li>Total Rows: ${total_count}</li>
            <li>Imported Rows: ${valid_count}</li> 
            <li>Invalid Rows: ${invalid_count}</li> 
            <li>Duplicate Rows: ${duplicate_count}</li>    
        </ul>
        <p>Click Download button to download invalid rows as a CSV?</p>
        <a href="app${download_invalid_link}" id="download-btn" class="btn bg-educ color-white">Download</a>
        <button id="cancel-btn" class="btn">Cancel</button>
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
