# JIMS - Jewellery Inventory Management System

A modern, comprehensive jewellery inventory management system designed to help jewellery businesses manage their stock, sales transactions, suppliers, and generate detailed reports with analytics.

## Features

### Core Functionality
- **Inventory Management**: Track jewellery items with detailed attributes (material, gemstone, weight, carat, etc.)
- **Sales Transactions**: Complete sales management with customer tracking and invoice generation
- **Supplier Management**: Manage supplier information and purchase orders
- **Customer Management**: Track customer information and purchase history
- **Reporting & Analytics**: Generate daily, monthly, and custom reports with profit calculations

### Key Features
- **Real-time Stock Tracking**: Monitor inventory levels with low-stock alerts
- **Multi-currency Support**: Configurable currency and tax settings
- **Barcode Integration**: SKU and barcode management for products
- **Search & Filter**: Advanced search and filtering capabilities
- **Responsive Design**: Mobile-first design that works on all devices
- **User Management**: Role-based access control
- **Data Export**: Export reports in various formats
- **Audit Trail**: Complete tracking of inventory movements

## Tech Stack

### Backend
- **PHP 7.4+**: Core backend logic and API endpoints
- **MySQL 5.7+**: Database management
- **PDO**: Secure database interactions

### Frontend
- **HTML5**: Semantic markup
- **CSS3**: Modern styling with CSS variables and grid/flexbox
- **JavaScript ES6+**: Interactive frontend functionality
- **Font Awesome**: Icon library

## Installation

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- Modern web browser

### Setup Instructions

1. **Clone/Download the Project**
   ```bash
   git clone <repository-url>
   cd jims
   ```

2. **Database Setup**
   - Create a new MySQL database named `jims_db`
   - Import the database schema:
     ```sql
   mysql -u username -p jims_db < database/schema.sql
     ```

3. **Configure Database Connection**
   - Edit `config/database.php`:
     ```php
     private $host = 'localhost';
     private $db_name = 'jims_db';
     private $username = 'your_db_username';
     private $password = 'your_db_password';
     ```

4. **Web Server Configuration**
   - Point your web server document root to the project directory
   - Ensure PHP error reporting is enabled for development

5. **File Permissions**
   ```bash
   chmod -R 755 /path/to/jims
   chmod -R 777 /path/to/jims/uploads  # If using file uploads
   ```

6. **Access the Application**
   - Open your browser and navigate to `http://localhost/jims`
   - Default login: `admin` / `admin123`

## Database Schema

The system uses a comprehensive database schema with the following main tables:

- **products**: Jewellery inventory items
- **categories**: Product categorization
- **suppliers**: Supplier information
- **customers**: Customer management
- **sales**: Sales transactions
- **sale_items**: Individual sale line items
- **purchase_orders**: Supplier purchase orders
- **inventory_movements**: Stock movement tracking
- **users**: System user management

## API Endpoints

### Products
- `GET /api/products.php` - List products
- `GET /api/products.php?id={id}` - Get single product
- `POST /api/products.php` - Create product
- `PUT /api/products.php?id={id}` - Update product
- `DELETE /api/products.php?id={id}` - Delete product

### Sales
- `GET /api/sales.php` - List sales
- `GET /api/sales.php?id={id}` - Get single sale
- `GET /api/sales.php?report=1` - Generate sales report
- `POST /api/sales.php` - Create sale
- `PUT /api/sales.php?id={id}` - Update sale
- `DELETE /api/sales.php?id={id}` - Delete sale

### Suppliers
- `GET /api/suppliers.php` - List suppliers
- `GET /api/suppliers.php?id={id}` - Get single supplier
- `POST /api/suppliers.php` - Create supplier
- `PUT /api/suppliers.php?id={id}` - Update supplier
- `DELETE /api/suppliers.php?id={id}` - Delete supplier

### Reports
- `GET /api/reports.php?type=dashboard` - Dashboard data
- `GET /api/reports.php?type=sales` - Sales reports
- `GET /api/reports.php?type=inventory` - Inventory reports
- `GET /api/reports.php?type=profit` - Profit analysis
- `GET /api/reports.php?type=low_stock` - Low stock report

## Usage Guide

### Managing Products
1. Navigate to **Products** in the sidebar
2. Click **Add Product** to create new inventory items
3. Fill in product details including SKU, name, category, pricing, and stock levels
4. Use the search and filter functions to find specific items
5. Edit or delete products using the action buttons

### Processing Sales
1. Go to **Sales** section
2. Click **New Sale** to create a transaction
3. Add products to the sale by scanning or searching
4. Enter customer information (optional)
5. Apply discounts or taxes as needed
6. Complete the sale to generate an invoice

### Supplier Management
1. Access **Suppliers** from the sidebar
2. Add new suppliers with contact and payment details
3. Create purchase orders for restocking
4. Track supplier performance and order history

### Generating Reports
1. Navigate to **Reports** section
2. Select report type (Sales, Inventory, Profit, etc.)
3. Choose date range and filters
4. View interactive charts and tables
5. Export reports in PDF or Excel format

## Configuration

### System Settings
Edit `config/database.php` to configure:
- Database connection parameters
- Currency and tax settings
- File upload limits
- Email configuration
- Backup settings

### Customization
- Modify CSS variables in `assets/css/style.css` for branding
- Add custom fields to database schema as needed
- Extend API endpoints for additional functionality
- Customize report templates

## Security Features

- **SQL Injection Protection**: Using prepared statements with PDO
- **XSS Prevention**: Input sanitization and output encoding
- **CSRF Protection**: Token-based request validation
- **Password Security**: Hashed password storage
- **Session Management**: Secure session handling
- **Access Control**: Role-based permissions

## Performance Optimization

- **Database Indexing**: Optimized queries for large datasets
- **Caching**: Implement caching for frequently accessed data
- **Lazy Loading**: Load data on demand for better performance
- **Image Optimization**: Compressed product images
- **Minification**: CSS and JavaScript minification

## Browser Support

- Chrome 80+
- Firefox 75+
- Safari 13+
- Edge 80+
- Mobile Safari (iOS 13+)
- Chrome Mobile (Android 8+)

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

## Support

For support and documentation:
- Check the `docs/` directory for detailed guides
- Review the API documentation in `api/` folder
- Check the database schema in `database/schema.sql`

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Version History

### v1.0.0 (Current)
- Initial release
- Core inventory management
- Sales and supplier management
- Basic reporting and analytics
- Responsive web interface

## Future Roadmap

- [ ] Mobile app (React Native)
- [ ] Advanced analytics dashboard
- [ ] Multi-store support
- [ ] E-commerce integration
- [ ] Barcode scanning app
- [ ] Advanced reporting features
- [ ] API rate limiting
- [ ] Two-factor authentication
- [ ] Data backup automation
- [ ] Email notifications

## Troubleshooting

### Common Issues

**Database Connection Error**
- Check database credentials in `config/database.php`
- Ensure MySQL server is running
- Verify database exists and user has permissions

**Page Not Loading**
- Check web server configuration
- Ensure PHP is properly installed
- Check file permissions

**Images Not Uploading**
- Verify upload directory permissions
- Check PHP upload limits in php.ini
- Ensure file size limits are appropriate

**Slow Performance**
- Add database indexes
- Enable query caching
- Optimize images
- Consider implementing pagination

For additional support, please check the error logs and contact the development team.

---

**JIMS** - Jewellery Inventory Management System  
*Efficiently manage your jewellery business with modern technology*
