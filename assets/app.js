import './bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */

// Import Bootstrap CSS via Asset Mapper
import 'bootstrap/dist/css/bootstrap.min.css';

// Import Bootstrap JS (includes Popper.js)
import 'bootstrap';

// Import custom styles (after Bootstrap to override properly)
import './styles/app.css';

console.log('LibraShelf - Assets loaded via Asset Mapper 🎉');
