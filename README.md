Order receipt Generator Web app
Web-based order dashboard using PHP, Bootstrap, and MySql. The dashboard will include a scrollable table according to scrren size for displaying order details, an order creation page with payment status and amount calculations, and database integration to store and manage orders.

# Key Features

1. Dashboard Main Page (`index.php`)
   - Table of Orders:
     - Display order details in a table format with the following columns:
       - Order ID
       - Customer Name
       - Shipping Address
       - Contact Information
       - Items Purchased
       - Price
       - Payment Method
       - Payment Status
       - Special Instructions
       - Created At
     - Implement a scrollable table to handle large datasets effectively.
   - Filtering and Sorting:
     - Allow filtering by payment status (e.g., Unpaid, Paid, Partial Paid).
     - Enable filtering based on date ranges such as Today, Yesterday, Last 7 Days, Last 30 Days, Last 90 Days, Last 365 Days.
     - Recent order should be in first.
   - Menu Options:
     - Create Order: Link to the create order page.
     - Export to Excel: Option to export the order data to an Excel file.
     - Delete Orders: Option to delete selected orders.
   - Actions:
     - Print Selected Orders: Option to print selected orders or can print individual order also.
     - Delete Selected Orders: Option to delete selected orders can delete individual order also.

2. Create Order Page (`create_order.php`)
   - Form Fields:
     - Full Name: Input field for the customer’s full name.
     - Shipping Address: Textarea for the customer’s shipping address.
     - Contact Information: Input field for the customer’s contact information.
     - Items Purchased: Textarea for listing items purchased.
     - Price: Input field for the total price.
     - Payment Method: Dropdown to select payment method (e.g., COD, Online).
     - Payment Status: Dropdown to select payment status (e.g., Unpaid, Paid, Partial Paid).
     - Paid Amount: Input field for the amount that has been paid (visible only if Payment Status is Partial Paid).
     - Remaining Amount: Calculated field displaying the amount remaining (visible only if Payment Status is Partial Paid).
     - Special Instructions: Textarea for any special instructions.
   - Order ID Generation:
     - Automatically generate an Order ID in the format YYMMDDXXXX (e.g., 2409070001), where the number increases sequentially based on the current date.
   - Amount Calculation:
     - Paid Amount: Input field to enter the amount paid.
     - Remaining Amount: Calculate as `Price - Paid Amount` if the payment status is Partial Paid.
   - Submission:
     - On form submission, validate and calculate the amount remaining based on the payment status.
     - Insert the order details into the database.
     - Redirect to the dashboard or show a success message.

3. Database Integration
   - Database Connection:
     - Connect to the Mysql database using PHP.
   - Database Tables:
     - Admin Table:
       - Columns: `id`, `name`, `email`, `password`, `username`
       - Automatically generate username based on the admin’s full name (e.g., John Doe -> johndoe).
     - Customer Table:
       - Columns: `id`, `full_name`, `shipping_address`, `contact_information`, `order_id`, `items_purchased`, `price`, `payment_method`, `payment_status`, `paid_amount`, `remaining_amount`, `special_instructions`, `created_at`
       - Automatically generate Order ID in the format YYMMDDXXX.
       - Calculate and store `remaining_amount` based on `price` and `paid_amount` if `payment_status` is Partial Paid.
   - CRUD Operations:
     - Create: Add new orders to the database from the create order page.
     - Read: Fetch and display orders on the dashboard.
     - Update: Update order details if necessary.
     - Delete: Remove selected orders from the database.

4. Bootstrap Integration
   - Table Styling:
     - Use Bootstrap classes to style the table and make it responsive.
     - Implement scrollable functionality for the table.
   - Form Styling:
     - Use Bootstrap form components for a consistent look and feel on the create order page.
   - Buttons and Alerts:
     - Use Bootstrap buttons for actions (e.g., Create Order, Export to Excel, Print).
     - Implement Bootstrap alerts for success or error messages.

5. User Experience
   - Scrollable Table:
     - Ensure the table is scrollable to handle large datasets, with fixed headers if necessary.
   - Form Validation:
     - Implement client-side and server-side validation for the create order form.
   - Dynamic Calculations:
     - Automatically calculate the remaining amount based on the entered price and paid amount when the payment status is Partial Paid.
   - Feedback and Notifications:
     - Provide clear feedback on form submission and actions taken (e.g., order creation success, deletion confirmation).

# Print Preview and Receipt Formatting Guide

This project includes a print preview feature that allows users to generate and print order receipts in a standardized format. Below are the key details for setting up and using the print preview functionality:

# 1. Receipt Format
- Each receipt is designed to be 3x4 inches in size.
- For multiple receipts, they are formatted to fit within an A4-sized page while maintaining their 3x4 inch dimensions.
- A border surrounds the A4-sized container to clearly define the printable area.

# 2. Logo Banner
- A logo banner is included at the top of each receipt. This replaces any placeholder text currently in use and provides branding consistency.

# 3. Download Button Placement
- The download button is positioned immediately before the `.a4-container` div. This allows users to easily download the print preview as a PDF or image.

# 4. Gap Between Receipts
- There is appropriate spacing between each receipt on the A4 page. Proper alignment in both the x and y directions ensures a clear and organized layout, enhancing print quality and readability.

# 5. Single Receipt Printing
- When a user selects only one order, the receipt is printed at its actual size of 3x4 inches without any format changes. This ensures the correct and intended print size for single orders.

# 6. CSS Styling
- The project uses CSS to style each receipt in the 3x4 inch format.
- When multiple orders are selected, receipts adjust to fit within an A4 page size while maintaining their 3x4 inch dimensions.

# 7. Print Preview and Functionality
- Users can select one or more orders and click the "Print Selected" button to see a print preview.
- The preview displays receipts in the 3x4 inch format and adjusts them for multiple receipts within an A4 page. This provides a visual representation of how they will appear when printed.

# 8. Page Handling for Multiple Receipts
- If more than 4 receipts (a 2x2 layout on an A4 page) are selected, the system automatically adds a new page within the print preview to accommodate additional receipts.
- Each A4 page contains a maximum of 4 receipts (2 horizontally and 2 vertically). This design ensures that receipts do not overlap and remain neatly organized across pages.
- Proper margins and spacing are maintained for consistency and professional presentation.

# 9. Usage
1. Select Orders: Choose one or more orders from the list.
2. Click "Print Selected": Initiates the print preview for the selected orders.
3. View and Adjust: Review the print preview, which shows how the receipts will be formatted and printed.
4. Download or Print: Use the download button or the browser's print function to save or print the receipts.

# Additional Considerations
- Ensure all data is sanitized and validated to prevent SQL injection and other security issues.
- Optimize database queries for performance, especially when handling large amounts of data.
- Test the dashboard and create order functionality thoroughly to ensure smooth operation and usability.

[Click Here to see receipt Model](https://replit.com/@ajayabishwokar1/Custom-Code)
