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
            border-radius: 0.5rem;
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

        select:hover {
            border-color: #c58ca8;
            box-shadow: 0 0 0 2px rgba(197, 140, 168, 0.5);
            /* Add shadow similar to focus state */
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
            border: 1px dashed gray;
            border-radius: 0.5rem;
            padding: 1.5rem;
            text-align: center;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .drop-zone.dragover {
            background-color: #f2e6ec;
        }

        select, option{
            padding-left: 15px;
        }
        .w-100 {
    width: 100%;
}

.w-100 {
    width: 100%;
}

.mb-4 {
    margin-bottom: 1.5rem;
}

.btn {
    background-color: #fff;
    border: 1px solid #8b0045;
    color: #8b0045;
    padding: 8px 12px;
    font-size: 14px;
}

.btn:hover {
    background-color: #8b0045;
    color: #fff;
}

.radio-btn {
    background-color: #8b0045;
    color: #fff;
    padding: 10px 20px;
    margin-right: 10px;
    cursor: pointer;
    border-radius: 5px;
}

.radio-btn:hover {
    background-color: #6e0037;
}

.d-none {
    display: none;
}
.search-bar{
    margin-bottom: 10px;
}

.radio-btn.active {
    background-color: #8b0045; 
    color: white; 
}
.radio-btn.inactive {
    background-color: #e0e0e0; 
    color: #8b0045; 
}


    </style>

    <div style="min-height: 720px;" class="container-max-height">
        <div class="row ">
            <div class="col-sm-12">
                <form action="{{ route('import') }}" method="POST" enctype="multipart/form-data">

                <div class="w-100 mb-4 d-flex justify-content-between align-items-center">
    <!-- Radio buttons positioned to the left end -->
    <div class="d-flex align-items-center">
    <label for="platformRadio" class="radio-btn inactive">Platform</label>
    <input type="radio" name="option" id="platformRadio" value="platform" class="d-none">
    
    <label for="rawRadio" class="radio-btn inactive">Raw</label>
    <input type="radio" name="option" id="rawRadio" value="raw" class="d-none">
</div>

    
    <!-- Get CSV Format button positioned to the right end -->
    <div id="raw-btn-container" class="d-none">
        <button class="btn hover-action" onclick="window.location.href='{{ route('get-csv') }}'">
            Get CSV Format
        </button>
    </div>
</div>

<div id="platform-container">
    <select id="platform" class="w-100 platforms search-bar d-none" name="platform">
        <option value="" selected disabled>Select platform</option>
        <option value="linkedin">LinkedIn</option>
        <option value="apollo">Apollo</option>
    </select>
</div>



                    <div class="text-center mb-4">
                        <div class="card-body justify-content-center align-items-center drop-zone" id="dropZone">
                            <div class="mx-5">
                                <h5 class="mb-4 font-educ">Drag and drop your files</h5>
                                <p class="mb-4" title="The uploaded file must be a file of type: csv">File
                                    formats we support <i class="fas fa-info-circle"></i></p>
                            </div>
                            @csrf
                            <div>
                                <input accept=".csv" type="file" name="csv_file" required id="fileInput" class="d-none">
                            </div>
                            <div>
                                <label for="fileInput" class="btn hover-action mt-4">Import Manually</label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="card d-none" id="file-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <img src="../images/csv.png" alt="" style="height:4rem;">
                        <p id="file-name" class="text-muted d-none"></p>
                    </div>
                    <div class="w-50">
                        <div class="progress" id="progressContainer" role="progressbar" aria-valuenow="0" aria-valuemin="0"
                            aria-valuemax="100">
                            <div class="progress-bar bg-educ" id="progressBar" style="width: 0%;"></div>
                        </div>
                        <p id="progress-message" class="text-muted d-none mt-2"></p>

                        <p id="error-message" class="text-danger d-none mt-2"></p>
                    </div>
                    <div>
                        <input type="submit" id="submit-btn" class="btn hover-action d-none"
                            style="margin-left: auto">
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        //-----------declaring----------------//
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
        
        document.addEventListener('DOMContentLoaded', function() {
        const platformRadio = document.getElementById('platformRadio');
        const rawRadio = document.getElementById('rawRadio');
        const platformSelect = document.getElementById('platform');
        const rawBtn = document.getElementById('raw-btn-container');

        platformRadio.addEventListener('change', function() {
            if (platformRadio.checked) {
                platformSelect.classList.remove('d-none');
                rawBtn.classList.add('d-none');
            }
        });

        rawRadio.addEventListener('change', function() {
            if (rawRadio.checked) {
                rawBtn.classList.remove('d-none');
                platformSelect.classList.add('d-none');
            }
        });
    });



        //----------Drag and drop--------------//
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

        //-----------form field validation----------//
        fileInput.addEventListener('change', handleFiles);
        document.getElementById('platform').addEventListener('change', handlePlatformChange);

        function handleFiles() {
            const fileNameText = fileInput.files[0].name;
            fileName.textContent = fileNameText;
            fileName.classList.remove('d-none');
            card.classList.remove('d-none');
            errorMessage.classList.add('d-none');
            progressContainer.classList.add('d-none');
            progressMessage.classList.add('d-none');

            checkIfReadyToSubmit();
        }

        function handlePlatformChange() {
            const platform = document.getElementById('platform').value;
            const rawBtnContainer = document.getElementById('raw-btn-container');

            checkIfReadyToSubmit();

            // Toggle the raw button container based on the platform selection
            if (platform === 'raw') {
                rawBtnContainer.classList.remove('d-none');
            } else {
                rawBtnContainer.classList.add('d-none');
            }
        }

        function checkIfReadyToSubmit() {
            const platform = document.getElementById('platform');
            const submitBtn = document.getElementById('submit-btn');

            // Show submit button if a valid platform is selected and a file is uploaded
            if (platform && platform.checkValidity() && fileInput.files.length > 0) {
                submitBtn.classList.remove('d-none');
            } else {
                submitBtn.classList.add('d-none');

                // Report validity if the platform exists but isn't valid
                if (platform) {
                    platform.reportValidity();
                }
            }
        }



        //----------submit-------------//
        submitBtn.addEventListener('click', (e) => {


            e.preventDefault();
            //create form data
            const formData = new FormData();
            formData.append('csv_file', fileInput.files[0]);
            formData.append('platform', document.getElementById('platform').value);

            submitBtn.classList.add('d-none');
            progressContainer.classList.remove('d-none')
            progressBar.style.width = '0%';
            progressMessage.textContent = 'Uploading...';
            progressMessage.classList.remove('d-none');
            errorMessage.classList.add('d-none'); // Hide any previous error message
            progressBar.style.width = '20%';

            fetch('{{ route('import') }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(errorData => {
                            // Handle 500 error with custom message from the server
                            if (response.status === 500) {
                                throw new Error(errorData.message || 'Internal Server Error');
                            }
                            // Handle 422 validation errors
                            if (response.status === 422) {
                                const errors = errorData.errors;
                                let errorText = '';
                                for (const [key, errorMessages] of Object.entries(errors)) {
                                    errorText += errorMessages;
                                }
                                errorMessage.innerHTML = errorText;
                                throw new Error(errorText || 'Validation Error');
                            }
                            throw new Error(errorData.message || 'Upload failed');
                        });
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
                    if (data.success) {

                        progressBar.style.width = '100%';
                        progressMessage.textContent = 'Upload complete!';


                        let valid_count = data.data.valid_count;
                        let invalid_count = data.data.invalid_count;
                        let duplicate_count = data.data.duplicate_count;
                        let total_count = valid_count + invalid_count + duplicate_count;

                        setTimeout(() => {


                            const {
                                invalid_rows,
                                duplicate_rows
                            } = data.data.file_links;

                            showDownloadPrompt(valid_count, invalid_count, duplicate_count,
                                total_count,
                                invalid_rows, duplicate_rows, );
                        }, 800);




                        progressMessage.classList.remove('d-none');
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



        //show download promt
        function showDownloadPrompt(valid_count, invalid_count, duplicate_count, total_count, invalid_rows_link,
            duplicate_rows_link) {
            // Create the modal element
            const downloadPrompt = document.createElement('div');

            downloadPrompt.style.position = 'fixed';
            downloadPrompt.style.top = '50%';
            downloadPrompt.style.left = '50%';
            downloadPrompt.style.transform = 'translate(-50%, -50%)';
            downloadPrompt.style.backgroundColor = '#fff';

            downloadPrompt.style.boxShadow = '0px 0px 10px rgba(0, 0, 0, 0.4)';
            downloadPrompt.style.zIndex = '1000';
            downloadPrompt.style.borderRadius = '8px';
            downloadPrompt.classList.add = 'modal-header'
            // downloadPrompt.style.width = '350px';
            // Add the logo image and text in a flex container
            const logoUrl = "{{ url('/images/02-EduCLaaS-Logo-Raspberry-300x94.png') }}";
            const headerContent = `
           <div style="
            padding: 15px; 
            background: linear-gradient(180deg, rgb(255, 180, 206) 0%, hsla(0, 0%, 100%, 1) 100%);
            border-radius: 8px 8px 0 0;
        "   class="d-flex justify-content-between align-items-center">
        <div>
                <p style=" margin-right: 30px; margin-bottom: 0;" class="headings">Upload complete!</p>
                </div>
                 <div>
                <img src="${logoUrl}" alt="Company Logo" style="height: 30px;">
                 </div>
        </div>
    `;
            const bodyContent = `
    <div style="padding : 20px" class="fonts">
        <ul style="padding-left: 20px;">
            <li>Total Rows: ${total_count}</li>
            <li>Imported Rows: ${valid_count}</li> 
            <li>Invalid Rows: ${invalid_count}
                ${invalid_rows_link ? `<a href="${invalid_rows_link}" id="download-invalid-btn" style="color: #007bff; text-decoration: underline;">Download</a>` : ''}
            </li> 
            <li>Duplicate Rows: ${duplicate_count}
                ${duplicate_rows_link ? `<a href="${duplicate_rows_link}" id="download-duplicate-btn" style="color: #007bff; text-decoration: underline;">Download</a>` : ''}
            </li>    
        </ul>
        <div class="text-end">
            <button id="cancel-btn" class="btn" style="background-color: #6c757d; color: white; padding: 5px 10px; border-radius: 4px;">Close</button>
        </div>
    </div>    
    `;

            downloadPrompt.innerHTML = `
        ${headerContent}
        ${bodyContent}
       
        
    `;


            // Append the modal to the body
            document.body.appendChild(downloadPrompt);

            // Show the modal
            downloadPrompt.style.display = 'block';

            // Handle cancel button click
            document.getElementById('cancel-btn').addEventListener('click', () => {
                downloadPrompt.remove();
            });
        }
       // Update the JavaScript to toggle classes based on selection
    document.addEventListener('DOMContentLoaded', function() {
    const platformRadio = document.getElementById('platformRadio');
    const rawRadio = document.getElementById('rawRadio');
    const platformSelect = document.getElementById('platform');
    const rawBtnContainer = document.getElementById('raw-btn-container');
    const platformBtn = document.querySelector('label[for="platformRadio"]');
    const rawBtn = document.querySelector('label[for="rawRadio"]');

    platformRadio.addEventListener('change', function() {
        if (platformRadio.checked) {
            platformSelect.classList.remove('d-none');
            platformSelect.style.display = 'block';
            rawBtnContainer.style.display = 'none';

            platformBtn.classList.add('active');
            platformBtn.classList.remove('inactive');
            rawBtn.classList.remove('active');
            rawBtn.classList.add('inactive');
        }
    });

    rawRadio.addEventListener('change', function() {
        if (rawRadio.checked) {
            platformSelect.style.display = 'none';
            rawBtnContainer.style.display = 'block';

            rawBtn.classList.add('active');
            rawBtn.classList.remove('inactive');
            platformBtn.classList.remove('active');
            platformBtn.classList.add('inactive');
        }
    });
});


    </script>
@endsection
