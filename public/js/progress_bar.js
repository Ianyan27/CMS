// function updateProgress() {
//     fetch('/buh/progress')
//         .then(response => {
//             if (!response.ok) {
//                 throw new Error(`HTTP error! status: ${response.status}`);
//             }
//             return response.json();
//         })
//         .then(data => {
//             if (data && typeof data.progress === 'number') {
//                 const progressBar = document.getElementById('progress-bar');
//                 const progressPercentage = data.progress;

//                 // Debugging: log the progress value
//                 console.log(`Progress fetched: ${progressPercentage}%`);

//                 // Update the progress bar's width and text content
//                 progressBar.style.width = progressPercentage + '%';
//                 progressBar.textContent = `Transferring: ${progressPercentage}%`;

//                 // Continue updating until we reach exactly 100%
//                 if (progressPercentage < 100) {
//                     setTimeout(updateProgress, 1000); // Update every 1 second

//                 } else {
//                     // Finalize display when progress reaches 100%
//                     progressBar.style.width = '100%';
//                     progressBar.textContent = 'Transferring: 100%';
//                 }
//             } else {
//                 console.error('Invalid data format:', data);
//             }
//         })
//         .catch(error => {
//             console.error('Error fetching progress:', error.message);
//         });
// }

// // Start updating the progress bar
// updateProgress();
