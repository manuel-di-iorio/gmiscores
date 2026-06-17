# GameMaker Italia - Platform Template

Basic template for creating new site areas

## File structure

- /includes/layout.php Basic page template
- /lib: Server components
- /pages: HTML pages rendered by the layout (structured in .view and .ctrl components)
- /api: API called via HTTP Request
- /models: Database entity classes
- /assets: Static files (images, CSS)
- The other PHP files in the root are the pages that will actually be called by the browser.

## .htaccess

Deny direct browser access to some subfolders and set the Altervista PHP engine.

## Env

The `.env` file is not shared on the GitHub repository, but you can find the default example in the `.emv.example` file.
