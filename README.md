## codeigniter app

### Requirements
- For install you should comply with [Codeigniter's 3 server requirements](https://codeigniter.com/userguide3/general/requirements.html). 
- Tested only on Apache server.
- Works with SQL Server and should work with MySql (only migration where tested with MySql), should have `sqlsrv` or `mysqli` drivers installed and enabled.
- Make sure to have `mod_rewrite` module enabled.
- Composer is required.

### Install
- Clone this repo.
- Move into root directory and run `composer install`
- Fill `application\config\database.php` with your database connection credentials
- Then run `php index.php migrate` on project's root directory. This will create the tables on your database (database and user must be created previously), will download all data from [mindicador.cl](https://mindicador.cl/) and save it to database. This should take a couple minutes to complete.
- Go to `http://localhost/codeigniter` or whatever `base_url` you setted on `application\config\config.php` 
- Click `Charts` or `UFCrud` for whatever you may want to try.

### Notes
- Database is designed whith 2 tables: `Indicators` and `Historical`. First one contains all available indicators from `https://mindicador.cl/api` endpoint, second one if filled with all historical data from `https://mindicador.cl/api/{tipo_indicador}/{yyyy}`. This way its easier to create mantainers for all indicators if needed and there is no need to create aditional tables if aditional indicators are ever added.
- There is one core Model `application\core\MY_Model.php` which contains all basic database operations (get, save, update, delete) which are extendend to Indicators and Historical models.
- There is only one controller for the app (`application\controllers\App.php`) because is pretty small.
- Migrate controller does just that, run migrations.

### Libraries used
- Datatables, jquery, bootstrap installed via composer
- Chart.js and Moment.js used vía cdn link, couldn't find a way to install it via composer and didn't wa't to download and add as asset.

### Codeigniter
- Wanted to try Codeigniter 4 but apparently there is no sql server's driver available yet.

##
There is a couple changes to do in order to improve the code but that's it, hope you like it.
