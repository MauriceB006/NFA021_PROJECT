<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bus Schedule</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
        /* Global Styles */
        :root {
            --primary-color: #10069F;
            --secondary-color: #3498db;
            --accent-color: #e74c3c;
            --light-gray: #ecf0f1;
            --dark-gray: #7f8c8d;
            --white: #ffffff;
            --shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        body {
            font-family: 'Montserrat', sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f3f4f6;
        }

        h1 {
            color: #2c3e50;
            text-align: center;
            margin-bottom: 20px;
            padding: 10px;
        }

        /* Header Styles */
        .header {
            background-color: var(--primary-color);
            color: white;
            padding: 8px 0;
            box-shadow: var(--shadow);
            position: relative;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 900px;
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

        /* Table Sorting Styles */
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

        /* Print Settings */
        @media print {
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>

<body class="flex flex-col items-center p-6 min-h-screen">

    <!-- Header -->
    <header class="header w-full">
        <div class="header-content">
            <a href="../indexV51.php" class="back-button">
                <i class="fas fa-chevron-left"></i> Back
            </a>
            <h1>
                <img src="../assets/images_v5/ACTC LOGO -02 SMALL.png" class="logo" alt="ACTC Public Transport" style="height: 50px;">
            </h1>
            <div style="width: 80px;"></div> <!-- Spacer for balance -->
        </div>
    </header>

    <!-- Page Title -->
    <h1 class="text-4xl font-bold text-gray-800 mb-6 text-center">Bus Schedule</h1>

    <!-- Action Buttons -->
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

    <!-- Bus Schedule Table -->
    <div class="bg-white p-8 rounded-xl shadow-lg w-full max-w-4xl overflow-x-auto rounded-lg shadow-inner border border-gray-200">
        <table id="busScheduleTable" class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider rounded-tl-lg" data-sort-col="line">
                        Line
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" data-sort-col="price">
                        Price ($)
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" data-sort-col="interval">
                        Time Intervals (min)
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider rounded-tr-lg" data-sort-col="comments">
                        Comments
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <!-- Example row 1 -->
                <tr class="hover:bg-gray-50 transition-colors duration-150">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">101 - Downtown Express</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">2.50</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">15-20</td>
                    <td class="px-6 py-4 text-sm text-gray-500">Peak hours every 15 min.</td>
                </tr>

                <!-- Example row 2 -->
                <tr class="hover:bg-gray-50 transition-colors duration-150">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">205 - Coastal Route</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">3.00</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">30-45</td>
                    <td class="px-6 py-4 text-sm text-gray-500">Scenic route, less frequent.</td>
                </tr>

                <!-- Example row 3 -->
                <tr class="hover:bg-gray-50 transition-colors duration-150">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">303 - University Shuttle</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">1.75</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">10-15</td>
                    <td class="px-6 py-4 text-sm text-gray-500">Weekdays only.</td>
                </tr>

                <!-- Example row 4 -->
                <tr class="hover:bg-gray-50 transition-colors duration-150">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">410 - Industrial Zone</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">2.75</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">25-30</td>
                    <td class="px-6 py-4 text-sm text-gray-500">Early morning & late afternoon.</td>
                </tr>

                <!-- Example row 5 -->
                <tr class="hover:bg-gray-50 transition-colors duration-150">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">500 - Night Owl</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">4.00</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">60-90</td>
                    <td class="px-6 py-4 text-sm text-gray-500">Night service with limited frequency.</td>
                </tr>
            </tbody>
        </table>
    </div>

</body>

</html>
