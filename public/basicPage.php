<!DOCTYPE html>
<html>
<head>
    <meta CHARSET="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>NPI test</title>

    <!-- style -->
    <link href="css/basic.css" rel="stylesheet" />

    <!-- js -->
    <script src="js/basic.js" type="text/javascript"></script>
</head>
<body>
    <p>this is the basic page access, js app load etc... do your stuff...</p><br/>
    <p>npi fetch all <a href="http://127.0.0.1/apple_NPI/public/npi/">click here</a></p>
    <p>npi fetch one <a href="http://127.0.0.1/apple_NPI/public/npi/1">click here</a></p>

    <img src="img/wave.png"/>

    <br/>
    <form action="http://127.0.0.1/apple_NPI/public/npi/5" method="post">
        <fieldset>
            <legend>Update example</legend>
            <input type="hidden" id="_method" name="_method" value="PUT" />

            <label for="npi_label">npi_label</label>
            <input type="text" id="npi_label" name="npi_label" value="iPhone 1 EMEA"/><br/>

            <label for="npi_product_level1">npi_product_level1</label>
            <input type="text" id="npi_product_level1" name="npi_product_level1" value="iPhone"/><br/>

            <input type="submit" name="Update" value="Update"><br/>
        </fieldset>
    </form>

    <br/>
    <form action="http://127.0.0.1/apple_NPI/public/npi/" method="post">
        <fieldset>
            <legend>Create example</legend>

            <label for="npi_label">npi_label</label>
            <input type="text" id="npi_label" name="npi_label" value="iPhone 20 EMEA"/><br/>

            <label for="npi_product_level1">npi_product_level1</label>
            <input type="text" id="npi_product_level1" name="npi_product_level1" value="iPhone"/><br/>

            <input type="submit" name="Create" value="Create">
        </fieldset>
    </form>

    <br/>
    <form action="http://127.0.0.1/apple_NPI/public/npi/6" method="post">
        <fieldset>
            <legend>Delete example</legend>
            <input type="hidden" id="_method" name="_method" value="DELETE" />
            <input type="submit" name="DELETE" value="DELETE">
        </fieldset>
    </form>
</body>
</html>