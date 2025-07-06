<!DOCTYPE html>
<!-- Declare the document type as HTML5 -->
<html lang="en">
<!-- Start of the HTML document, language set to English -->
<head>
    <!-- Head section contains metadata about the document -->
    <meta charset="UTF-8">
    <!-- Specify the character encoding for the document -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Configure the viewport for responsive design across devices -->
    <title>Bus Schedule</title>
    <!-- Set the title of the web page, displayed in the browser tab -->
    <!-- Tailwind CSS CDN for styling -->
    <!-- Link to the Tailwind CSS content delivery network for utility-first styling -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts - Inter for a modern look -->
    <!-- Link to Google Fonts to import the 'Inter' font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        /* Start of custom CSS styles */
        body {
            /* Style for the body element */
            font-family: 'Inter', sans-serif;
            /* Set the primary font to Inter */
            background-color: #f3f4f6; /* Light gray background */
            /* Set a light gray background color */
        }
        /* Custom styles for table headers to indicate sortability */
        th {
            /* Styles for table header cells */
            cursor: pointer;
            /* Change cursor to pointer to indicate clickability */
            position: relative;
            /* Set position to relative for absolute positioning of child elements */
            padding-right: 2rem; /* Space for sort icon */
            /* Add padding to the right for sorting icons */
        }
        th.sort-asc::after {
            /* Styles for the sort-asc state (ascending order) */
            content: '▲';
            /* Display an upward triangle icon */
            position: absolute;
            /* Position the icon absolutely */
            right: 0.5rem;
            /* Position from the right edge */
            font-size: 0.8em;
            /* Set font size for the icon */
            color: #4b5563; /* Gray-700 */
            /* Set icon color */
        }
        th.sort-desc::after {
            /* Styles for the sort-desc state (descending order) */
            content: '▼';
            /* Display a downward triangle icon */
            position: absolute;
            /* Position the icon absolutely */
            right: 0.5rem;
            /* Position from the right edge */
            font-size: 0.8em;
            /* Set font size for the icon */
            color: #4b5563; /* Gray-700 */
            /* Set icon color */
        }
        /* Hide print buttons when printing */
        @media print {
            /* Styles applied specifically when the page is printed */
            .no-print {
                /* Rule for elements with 'no-print' class */
                display: none !important;
                /* Hide these elements completely from the print output */
            }
        }


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
        }
        
        h1 {
            color: #2c3e50;
            text-align: center;
            margin-bottom: 20px;
            padding: 10px;
        }    
        
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
    </style>
</head>

   <header class="header">
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

<body class="flex flex-col items-center p-6 min-h-screen">
    <!-- Body section with Tailwind classes for layout and spacing -->

    <div class="bg-white p-8 rounded-xl shadow-lg w-full max-w-4xl">
        <!-- Main container div with white background, padding, rounded corners, shadow, and responsive width -->
        <h1 class="text-4xl font-bold text-gray-800 mb-6 text-center">Bus Schedule</h1>
        <!-- Main heading for the page -->

        <!-- Action Buttons -->
        <!-- Container for action buttons -->
        <div class="no-print flex flex-wrap justify-center gap-4 mb-8">
            <!-- Div for buttons, hidden during print, flex layout, spacing, and margin-bottom -->
            <button id="downloadPdfBtn" class="bg-red-600 hover:bg-red-700 text-white font-semibold py-3 px-6 rounded-lg shadow-md transition duration-300 ease-in-out transform hover:scale-105">
                <!-- Button for downloading PDF, with Tailwind classes for styling and hover effects -->
                Download as PDF
                <!-- Button text -->
            </button>
            <button id="downloadExcelBtn" class="bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-6 rounded-lg shadow-md transition duration-300 ease-in-out transform hover:scale-105">
                <!-- Button for downloading Excel, with Tailwind classes for styling and hover effects -->
                Download as Excel
                <!-- Button text -->
            </button>
            <button id="printBtn" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg shadow-md transition duration-300 ease-in-out transform hover:scale-105">
                <!-- Button for printing, with Tailwind classes for styling and hover effects -->
                Print Table
                <!-- Button text -->
            </button>
        </div>

        <!-- Bus Schedule Table -->
        <!-- Container for the bus schedule table -->
        <div class="overflow-x-auto rounded-lg shadow-inner border border-gray-200">
            <!-- Div for horizontal scrolling on small screens, rounded corners, shadow, and border -->
            <table id="busScheduleTable" class="min-w-full divide-y divide-gray-200">
                <!-- Table element with full width, row dividers -->
                <thead class="bg-gray-50">
                    <!-- Table header section with light gray background -->
                    <tr>
                        <!-- Table header row -->
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider rounded-tl-lg" data-sort-col="line">
                            <!-- Table header for 'Line' column, sortable -->
                            Line
                            <!-- Column title -->
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" data-sort-col="price">
                            <!-- Table header for 'Price' column, sortable -->
                            Price ($)
                            <!-- Column title -->
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" data-sort-col="interval">
                            <!-- Table header for 'Time Intervals' column, sortable -->
                            Time Intervals (min)
                            <!-- Column title -->
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider rounded-tr-lg" data-sort-col="comments">
                            <!-- Table header for 'Comments' column, sortable -->
                            Comments
                            <!-- Column title -->
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <!-- Table body section with white background and row dividers -->
                    <!-- Sample Data -->
                    <!-- Example row 1 -->
                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                        <!-- Table row with hover effect -->
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">101 - Downtown Express</td>
                        <!-- Cell for Line -->
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">2.50</td>
                        <!-- Cell for Price -->
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">15-20</td>
                        <!-- Cell for Time Intervals -->
                        <td class="px-6 py-4 text-sm text-gray-500">Peak hours every 15 min.</td>
                        <!-- Cell for Comments -->
                    </tr>
                    <!-- Example row 2 -->
                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                        <!-- Table row with hover effect -->
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">205 - Coastal Route</td>
                        <!-- Cell for Line -->
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">3.00</td>
                        <!-- Cell for Price -->
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">30-45</td>
                        <!-- Cell for Time Intervals -->
                        <td class="px-6 py-4 text-sm text-gray-500">Scenic route, less frequent.</td>
                        <!-- Cell for Comments -->
                    </tr>
                    <!-- Example row 3 -->
                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                        <!-- Table row with hover effect -->
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">303 - University Shuttle</td>
                        <!-- Cell for Line -->
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">1.75</td>
                        <!-- Cell for Price -->
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">10-15</td>
                        <!-- Cell for Time Intervals -->
                        <td class="px-6 py-4 text-sm text-gray-500">Weekdays only.</td>
                        <!-- Cell for Comments -->
                    </tr>
                    <!-- Example row 4 -->
                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                        <!-- Table row with hover effect -->
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">410 - Industrial Zone</td>
                        <!-- Cell for Line -->
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">2.75</td>
                        <!-- Cell for Price -->
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">25-30</td>
                        <!-- Cell for Time Intervals -->
                        <td class="px-6 py-4 text-sm text-gray-500">Early morning & late afternoon.</td>
                        <!-- Cell for Comments -->
                    </tr>
                    <!-- Example row 5 -->
                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                        <!-- Table row with hover effect -->
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">500 - Night Owl</td>
                        <!-- Cell for Line -->
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">4.00</td>
                        <!-- Cell for Price -->
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">60-90</td>
                        <!-- Cell for Time Intervals -->
                        <td class="px-6 py-4 text-sm text-gray-