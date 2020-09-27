## codeigniter app

### Requirements
- For install you should comply with [Codeigniter's 3 server requirements](https://codeigniter.com/userguide3/general/requirements.html). 
- Apache server.
- Works with SQL Server and should work with MySql (only migration where tested with MySql, probably will be bugs with date formats and conversions), should have `sqlsrv` or `mysqli` drivers installed and enabled.
- Make sure to have `mod_rewrite` module enabled.
- Composer is required.

### Install
- Clone this repo.
- Move into root directory and run `composer install`
- Fill `application\config\database.php` with your database connection credentials
- Then run `php index.php migrate` on project's root directory. This will create the tables on your database (database and user must be created previously), will download all data from [mindicador.cl](https://mindicador.cl/) and save it to database. This should take a couple minutes to complete.
- Go to `http://localhost/codeigniter` or whatever `base_url` you setted on `application\config\config.php` 
- Click `Charts` or `UF Crud` for whatever you may want to try.

### Notes
- Database is designed whith 2 tables: `Indicators` and `Historical`. First one contains all available indicators from `https://mindicador.cl/api` endpoint, second one is filled with all historical data from `https://mindicador.cl/api/{tipo_indicador}/{yyyy}`. This way it's easier to create mantainers for all indicators if needed and there is no need to create aditional tables if aditional indicators were ever added.
- There is one core Model `application\core\MY_Model.php` which contains all basic database operations (get, save, update, delete) which are extendend to Indicators and Historical models.
- There is only one controller for the app (`application\controllers\App.php`) because it's pretty small.
- Migrate controller does just that, run migrations.

### Libraries used
- Datatables, jquery, bootstrap, GuzzleHttp installed via composer.
- Chart.js and Moment.js used vía cdn link.
