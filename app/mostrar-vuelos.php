<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="css/styles.css"/>
        <link rel="stylesheet" href="lib/bootstrap-3.3.7/css/bootstrap.min.css"/>
        <link rel="stylesheet" href="lib/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css"/>
        <script src="lib/jquery-3.1.1/jquery-3.1.1.min.js"></script>
        <script src="lib/bootstrap-3.3.7/js/bootstrap.min.js"></script>
        <script src="lib/momentjs/moment.min.js"></script>
        <script src="lib/momentjs/locale/es.js"></script>
        <script src="lib/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"></script>
        <script src="js/app.js"></script>
    </head>
    <body>
        <img id="imagenFondo" src="img/banner.jpg">
        <div class="contenido">
            <div class="contenido-transparente-relative">
                <div class="row">
                    <div class="col-md-3 col-sm-3 col-xs-3">
                        <a class="" href="#" onclick="location.href=getAbsolutePath()">
                            <img id="logoEmpresa" src="img/logo.gif"/>
                            <p class="titulo"><i>Buscalo.com</i></p>
                        </a>
                    </div>
                </div>
                <div class="row container-fluid">
                    <center>
                        <div id="buscador-vuelo-panel" class="col-md-12 col-sm-12 col-xs-12"></div>
                    </center>
                </div>
                <div class="row container-fluid">
                    <br/>
                    <center>
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <div class="form-buscar-vuelo">
                                <?php
                                $xml = simplexml_load_file("vuelos.xml");

                                $products = $xml->xpath("/vuelos/vuelo");
                                if (count($products) > 0){
                                ?>
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Aereolínea</th>
                                            <th>Aereopuerto</th>
                                            <th>Ciudad de origen</th>
                                            <th>Ciudad de destino</th>
                                            <th>Fecha</th>
                                            <th>Hora</th>
                                            <th>Precio</th>
                                        </tr>
                                    </thead>
                                <?php
                                    foreach ($products as $product)
                                    {
                                        print('<tr>');
                                        print('<td>'.$product->aerolinea.'</td>');
                                        print('<td>'.$product->aeropuerto.'</td>');
                                        print('<td>'.$product->lugar_origen.'</td>');
                                        print('<td>'.$product->lugar_destino.'</td>');
                                        print('<td>'.$product->fecha.'</td>');
                                        print('<td>'.$product->hora.'</td>');
                                        print('<td>$'.$product->precio.'</td>');
                                        print('</tr>');
                                    }?>
                                </table>
                                <?php
                                }else{
                                    echo '<h3 align="center">LO SENTIMOS :(</h3><center>No hemos podido encontrar resultados segun los criterios de busqueda.</center>';
                                }
                                ?>
                            </div>
                            <br/>
                            <br/>
                            <br/>
                        </div>
                    </center>
                </div>
            </div>
        </div>
        <div id="modal-content-body"></div>
    </body>
</html>