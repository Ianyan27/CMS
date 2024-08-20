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
                                <div class="progress" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                                    <div class="progress-bar bg-purple" id="progressBar" style="width: 0%;"></div>
                                </div>
                            </div>
                            <div class="col-sm-4 ">
                                <button type="submit" id="submit-btn" class="btn bg-educ color-white d-none" style="margin-left: auto">Submit</button>
                            </div>
                        </div>
                    
                        <p id="progress-message" class="text-muted d-none mt-2"></p>
                        <p id="error-message" class="text-danger d-none mt-2"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
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
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
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
    </script>
@endsection
