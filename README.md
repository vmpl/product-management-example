## Technology requirements:
* Laravel: 9+
* SQL database
* VueJS: 2 or 3
* Usage of component libraries / CSS frameworks is optional • Styling: SCSS
* Usage of design patterns (i.e. BEM, Atomic Design) is optional
## Workflow requirements:
* Repository: Please set up a GitHub repository (public or private)
* Commits: Please commit often, at least daily
* Create a new Laravel project
## Data points:
1. Product:
* ID (integer)
* name (string)
* number (integer)
* created at (timestamp) o updated at (timestamp)
2. Pack:
* ID (integer)
* name (string)
* created at (timestamp) o updated at (timestamp)

A pack is a group of products and must consist of at least two products. Products may exist without being included in any packs.
## Features:
* UI for managing product and pack data
  * SPA
    * You may implement the SPA either decoupled or monolithic (i.e.backend routing, frontend mounted on backend templates)
  * I18n (English translations suffice)
  * Authentication
    * Login with username and password
  * General layout:
    * Header with navigation, log out button
    * Content underneath
  * Product management pages
    * Overview page
      * Button for creating new products
      * Table
        * Sorting and search
        * One column per data point
        * Additional column for action buttons: edit, delete
      * Create/edit page
        * Form for editing product data (FE and BE validation)
          * Product name is required
  * Pack management pages
    * Overview page
      * Button for creating new packs
      * Table
        * Sorting and search
        * One column per data point
        * Additional column for action buttons: edit, delete
    * Create/edit page
      * Form for editing pack data (FE and BE validation)
        * Pack name is required
        * Multi-select for selecting products (at least two products are
          required)
* External API for exposing product and pack data to API consumers
  * Endpoint for authentication
    * ClientID and secret
  * Endpoints for products GET item and GET collection
    * Format: JSON
  * Endpoints for packs GET item and GET collection
    * Format: JSON
    * Response should include products of packs
      * Normalized data structure (one array for packs,one array for products)
