<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bus Schedule</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- jsPDF and html2canvas -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6;
        }

        th {
            cursor: pointer;
            position: relative;
            padding-right: 2rem;
        }

        th.sort-asc::after {
            content: '▲';
            position: absolute;
            right: 0.5rem;
            font-size: 0.8em;
            color: #4b5563;
        }

        th.sort-desc::after {
            content: '▼';
            position: absolute;
            right: 0.5rem;
            font-size: 0.8em;
            color: #4b5563;
        }

        @media print {
            .no-print {
                display: none !important;
            }
        }

        :root {
            --primary-color: #10069F;
            --shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .header {
            width: 100%;
            background-color: var(--primary-color);
            color: white;
            padding: 8px 0;
            box-shadow: var(--shadow);
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
        }

        .back-button {
            background-color: transparent;
            color: white;
            border: 1px solid white;
            padding: 6px 12px;
            border-radius: 20px;
            cursor: pointer;
            font-size: 12px;
            font-weight: 500;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 5px;
            text-decoration: none;
        }

        .back-button:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }
    </style>
</head>

<body class="flex flex-col items-center p-6 min-h-screen">

    <header class="header">
        <div class="header-content">
            <a href="../indexV51.php" class="back-button">
                <i class="fas fa-chevron-left"></i> Back
            </a>
            <h1>
                <img src="../assets/images_v5/ACTC LOGO -02 SMALL.png" class="logo" alt="ACTC Public Transport" style="height: 50px;">
            </h1>
            <div style="width: 80px;"></div>
        </div>
    </header>

    <div class="bg-white p-8 rounded-xl shadow-lg w-full max-w-4xl mt-8">
        <h1 class="text-4xl font-bold text-gray-800 mb-6 text-center">Bus Schedule</h1>

        <div class="no-print flex flex-wrap justify-center gap-4 mb-8">
            <button id="downloadPdfBtn" class="bg-red-600 hover:bg-red-700 text-white font-semibold py-3 px-6 rounded-lg shadow-md transition duration-300 ease-in-out transform hover:scale-105">
                Download as PDF
            </button>
            <button id="downloadExcelBtn" class="bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-6 rounded-lg shadow-md transition duration-300 ease-in-out transform hover:scale-105">
                Download as Excel
            </button>
            <button id="printBtn" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg shadow-md transition duration-300 ease-in-out transform hover:scale-105">
                Print Table
            </button>
        </div>

        <div class="overflow-x-auto rounded-lg shadow-inner border border-gray-200">
            <table id="busScheduleTable" class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider rounded-tl-lg" data-sort-col="line">Line</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" data-sort-col="price">Price ($)</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" data-sort-col="interval">Time Intervals (min)</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider rounded-tr-lg" data-sort-col="comments">Comments</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Line B1</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">200.000 LBP</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">15-20</td>
                        <td class="px-6 py-4 text-sm text-gray-500">Peak hours every 15 min.</td>
                    </tr>
                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Line B2</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">200.000</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">30-45</td>
                        <td class="px-6 py-4 text-sm text-gray-500">Scenic route, less frequent.</td>
                    </tr>

                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Line B3</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">180.000</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">30-45</td>
                        <td class="px-6 py-4 text-sm text-gray-500">If Any Delays Occur Please Contact Support.</td>
                    </tr>
                    

                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Line ML3</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">250.000</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">30-45</td>
                        <td class="px-6 py-4 text-sm text-gray-500">New Route</td>
                    </tr>
                    

                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">All Lines Start At 7:00 AM and stops at 7:00 PM</td>
                    </tr>
                    <!-- Add more rows as needed -->
                </tbody>
            </table>
        </div>
    </div>

    <script>
        document.getElementById('downloadPdfBtn').addEventListener('click', function () {
            html2canvas(document.getElementById('busScheduleTable')).then(canvas => {
                const imgData = canvas.toDataURL('image/png');
                const pdf = new jspdf.jsPDF();
                const imgProps = pdf.getImageProperties(imgData);
                const pdfWidth = pdf.internal.pageSize.getWidth();
                const pdfHeight = (imgProps.height * pdfWidth) / imgProps.width;

                pdf.addImage(imgData, 'PNG', 0, 0, pdfWidth, pdfHeight);
                pdf.save('bus-schedule.pdf');
            });
        });

        document.getElementById('downloadExcelBtn').addEventListener('click', function () {
            let table = document.getElementById('busScheduleTable');
            let rows = table.querySelectorAll('tr');
            let csv = [];

            rows.forEach(row => {
                let cols = row.querySelectorAll('td, th');
                let rowData = [];
                cols.forEach(col => rowData.push('"' + col.innerText + '"'));
                csv.push(rowData.join(','));
            });

            let csvFile = new Blob([csv.join('\n')], { type: 'text/csv' });
            let downloadLink = document.createElement('a');
            downloadLink.download = 'bus-schedule.csv';
            downloadLink.href = window.URL.createObjectURL(csvFile);
            downloadLink.style.display = 'none';
            document.body.appendChild(downloadLink);
            downloadLink.click();
        });

        document.getElementById('printBtn').addEventListener('click', function () {
            window.print();
        });
    </script>

</body>

</html>
