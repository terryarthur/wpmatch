# WPMatch - WordPress Plugin & Theme

A comprehensive WordPress plugin and theme solution for matching functionality. WPMatch provides a complete platform for creating matching experiences within WordPress websites.

## 🚀 Features

### Plugin Features
- **Matching Algorithm**: Core matching functionality with customizable scoring
- **Shortcode Support**: Easy integration with `[wpmatch]` shortcode
- **AJAX Loading**: Smooth user experience with asynchronous loading
- **Admin Dashboard**: Complete administration interface
- **Database Management**: Custom tables for matches and user preferences
- **Extensible Architecture**: Hooks and filters for customization

### Theme Features
- **Responsive Design**: Mobile-first, fully responsive layout
- **Match Profile Support**: Custom post type for user profiles
- **Compatibility Scoring**: Visual compatibility indicators
- **Customizer Integration**: Theme options via WordPress Customizer
- **Modern UI**: Clean, contemporary design
- **Accessibility Ready**: WCAG compliant markup

## 📁 Project Structure

```
wpmatch/
├── plugin/                    # WordPress Plugin
│   ├── wpmatch.php           # Main plugin file
│   ├── includes/             # Plugin classes
│   │   ├── class-wpmatch-admin.php
│   │   ├── class-wpmatch-frontend.php
│   │   └── class-wpmatch-database.php
│   └── assets/               # Plugin assets
│       ├── css/
│       │   ├── frontend.css
│       │   └── admin.css
│       └── js/
│           ├── frontend.js
│           └── admin.js
├── theme/                    # WordPress Theme
│   ├── style.css            # Main stylesheet
│   ├── functions.php        # Theme functions
│   ├── index.php           # Main template
│   ├── header.php          # Header template
│   ├── footer.php          # Footer template
│   ├── sidebar.php         # Sidebar template
│   ├── inc/                # Theme includes
│   │   ├── template-functions.php
│   │   └── customizer.php
│   └── assets/             # Theme assets
│       ├── css/
│       │   └── main.css
│       └── js/
│           └── main.js
├── package.json            # Node.js dependencies
├── composer.json           # PHP dependencies
├── .gitignore             # Git ignore rules
└── README.md              # This file
```

## 🛠 Installation

### Prerequisites
- WordPress 5.0 or higher
- PHP 7.4 or higher
- MySQL 5.6 or higher

### Plugin Installation

1. **Download the plugin:**
   ```bash
   git clone https://github.com/terryarthur/wpmatch.git
   cd wpmatch
   ```

2. **Install dependencies:**
   ```bash
   npm install
   composer install
   ```

3. **Build assets:**
   ```bash
   npm run build
   ```

4. **Upload to WordPress:**
   - Copy the `plugin` folder to `/wp-content/plugins/wpmatch/`
   - Activate the plugin in WordPress admin

### Theme Installation

1. **Upload theme:**
   - Copy the `theme` folder to `/wp-content/themes/wpmatch-theme/`
   - Activate the theme in WordPress admin

2. **Configure theme:**
   - Go to Customize → WPMatch Theme Options
   - Configure your matching settings

## 💡 Usage

### Basic Shortcode Usage

Display matches on any page or post:

```php
[wpmatch]
```

With custom attributes:
```php
[wpmatch type="featured" limit="10"]
```

### Theme Integration

The theme automatically supports match profiles:

1. **Create Match Profiles:**
   - Go to WordPress admin → Match Profiles
   - Add new profiles with details

2. **Display Profiles:**
   - Profiles are automatically displayed on `/profiles/` page
   - Individual profiles accessible at `/profiles/profile-name/`

### Plugin Settings

Configure the plugin:

1. Go to WordPress admin → WPMatch
2. Enable/disable matching functionality
3. Configure matching parameters
4. View statistics and manage matches

## 🎨 Customization

### Theme Customization

The theme supports extensive customization through the WordPress Customizer:

- **Header Settings**: Layout options, site description visibility
- **Matching Settings**: Profiles per page, compatibility display
- **Color Settings**: Primary and secondary colors
- **Footer Settings**: Custom footer text

### Plugin Hooks

Developers can extend functionality using hooks:

```php
// Filter match results
add_filter( 'wpmatch_get_matches', 'custom_match_filter', 10, 3 );

// Action after successful match
add_action( 'wpmatch_match_created', 'custom_match_handler', 10, 2 );

// Customize compatibility calculation
add_filter( 'wpmatch_compatibility_score', 'custom_compatibility', 10, 3 );
```

### CSS Customization

Override styles in your child theme or custom CSS:

```css
/* Customize match cards */
.wpmatch-match {
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

/* Change primary color */
:root {
    --wpmatch-primary-color: #your-color;
}
```

## 🔧 Development

### Setting Up Development Environment

1. **Clone the repository:**
   ```bash
   git clone https://github.com/terryarthur/wpmatch.git
   cd wpmatch
   ```

2. **Install dependencies:**
   ```bash
   npm install
   composer install
   ```

3. **Start development:**
   ```bash
   npm run watch
   ```

### Available Scripts

- `npm run build`: Build production assets
- `npm run watch`: Watch for changes and rebuild
- `npm run lint`: Run ESLint
- `npm run lint:fix`: Fix ESLint errors
- `npm run format`: Format code with Prettier
- `composer phpcs`: Run PHP Code Sniffer
- `composer phpcbf`: Fix PHP coding standards

### Testing

Run PHP tests:
```bash
composer test
```

## 📊 Database Schema

### wpmatch_matches
Stores match relationships between users.

| Column | Type | Description |
|--------|------|-------------|
| id | mediumint(9) | Primary key |
| user_id | bigint(20) | User ID |
| matched_user_id | bigint(20) | Matched user ID |
| match_score | decimal(5,2) | Compatibility score |
| status | varchar(20) | Match status |
| created_at | datetime | Creation timestamp |
| updated_at | datetime | Update timestamp |

### wpmatch_user_preferences
Stores user preferences for matching.

| Column | Type | Description |
|--------|------|-------------|
| id | mediumint(9) | Primary key |
| user_id | bigint(20) | User ID |
| preference_key | varchar(100) | Preference identifier |
| preference_value | longtext | Preference data |
| created_at | datetime | Creation timestamp |
| updated_at | datetime | Update timestamp |

## 🤝 Contributing

We welcome contributions! Please follow these steps:

1. **Fork the repository**
2. **Create a feature branch:**
   ```bash
   git checkout -b feature/amazing-feature
   ```
3. **Make your changes and commit:**
   ```bash
   git commit -m 'Add amazing feature'
   ```
4. **Push to the branch:**
   ```bash
   git push origin feature/amazing-feature
   ```
5. **Open a Pull Request**

### Coding Standards

- Follow WordPress Coding Standards
- Use PHPDoc for documentation
- Write meaningful commit messages
- Include tests for new functionality

## 📝 License

This project is licensed under the GPL v2 or later - see the [LICENSE](LICENSE) file for details.

## 🆘 Support

- **Documentation**: [Wiki](https://github.com/terryarthur/wpmatch/wiki)
- **Issues**: [GitHub Issues](https://github.com/terryarthur/wpmatch/issues)
- **Discussions**: [GitHub Discussions](https://github.com/terryarthur/wpmatch/discussions)

## 🗺 Roadmap

### Version 1.1
- [ ] Advanced matching algorithms
- [ ] Email notifications
- [ ] Mobile app integration
- [ ] Multi-language support

### Version 1.2
- [ ] Real-time chat system
- [ ] Video call integration
- [ ] Advanced admin analytics
- [ ] API endpoints

### Version 2.0
- [ ] Machine learning matching
- [ ] Social media integration
- [ ] Premium features
- [ ] White-label solutions

## 🙏 Acknowledgments

- WordPress community for the excellent platform
- Contributors and beta testers
- Open source libraries used in this project

## 📞 Contact

- **Author**: Terry Arthur
- **Email**: terryarthur@gmail.com
- **GitHub**: [@terryarthur](https://github.com/terryarthur)
- **Website**: [terryarthur.com](https://terryarthur.com)

---

Made with ❤️ for the WordPress community
