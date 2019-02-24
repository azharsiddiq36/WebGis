<!doctype html>
<html lang="en">
  <head>
    <link rel="stylesheet" href="assets/css/ol.css" type="text/css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
      #marker {
          width: 20px;
          height: 20px;
          border: 1px solid #088;
          border-radius: 10px;
          background-color: #0FF;
          opacity: 0.5;
        } 
      .map {
          height: 500px;
          width: 100%;
      }
      .popover-content {
          min-width: 180px;
      }
      .gambarnya{
        height: 150px;
        width: 100%;
      }
    </style>
    <script src="assets/js/ol-debug.js" type="text/javascript"></script>
    <title>OpenLayers example</title>
  </head>
  <body>
    <h2>My Map</h2>
    <div class="map" id="map"></div>
    <div id="popup" class="ol-popup">
        <a href="#" id="popup-closer" class="ol-popup-closer"></a>
        <div id="popup-content"></div>
    </div>
    <script type="text/javascript">
      var styles = {
        'Point': [new ol.style.Style({
            image: new ol.style.Icon(/** @type {olx.style.IconOptions} */ ({
                  anchor: [0.5, 50],
                  // size: [100, 100],
                  // offset: [0, 0],
                  // opacity: 1,
                  scale: 0.5,
                  anchorXUnits: 'fraction',
                  anchorYUnits: 'pixels',
                  src: 'assets/img/hi.png'
                }))
        })]
        ,
        'LineString': [new ol.style.Style({
            stroke: new ol.style.Stroke({
                color: 'green',
                width: 1
            })
        })],
        'Polygon': [new ol.style.Style({
            stroke: new ol.style.Stroke({
                color: 'blue',
                lineDash: [4],
                width: 3
            }),
            fill: new ol.style.Fill({
                color: 'rgba(0, 0, 255, 0.1)'
            })
        })],
        'Circle': [new ol.style.Style({
            stroke: new ol.style.Stroke({
                color: 'red',
                width: 2
            }),
            fill: new ol.style.Fill({
                color: 'rgba(255,0,0,0.2)'
            })
        })
        ]
    };

    var styleFunction = function(feature, resolution) {
      return styles[feature.getGeometry().getType()];
    };

      var vectorLayer = new ol.layer.Vector({
      source: new ol.source.Vector({
      url: 'data/spbu.geojson', format : new ol.format.GeoJSON({featureProjection:"EPSG:4326"})
      }),
      style: styleFunction
    }); 

    var kecamatanLayer = new ol.layer.Image({
        source: new ol.source.ImageWMS({
          url: "http://localhost/mapserver/mapserv.exe?map=C:/xampp/htdocs/web-gis/data/kecamatan.map",
          serverType: "mapserver",
          params: {
            LAYERS: "Kecamatan",
            FORMAT: "image/png"
          }
        })
      });
      var jalanLayer = new ol.layer.Image({
        source: new ol.source.ImageWMS({
          url: "http://localhost/mapserver/mapserv.exe?map=C:/xampp/htdocs/web-gis/data/jalan.map",
          serverType: "mapserver",
          params: {
            LAYERS: "jalan",
            FORMAT: "image/png"
          }
        })
      });
      var rasterLayer = new ol.layer.Tile({
            source: new ol.source.OSM()
          });
    var map = new ol.Map({
      target: 'map',
      layers: [
        rasterLayer, kecamatanLayer, jalanLayer, vectorLayer
      ],
      view: new ol.View({
        //center: ol.proj.fromLonLat([101.468675,0.481528]),
        center: ol.proj.fromLonLat([101.4499703,0.5511114]),
        zoom: 11
      })
    });

    //Pop up
    var
        container = document.getElementById('popup'),
        content_element = document.getElementById('popup-content'),
        closer = document.getElementById('popup-closer');

    closer.onclick = function() {
        overlay.setPosition(undefined);
        closer.blur();
        return false;
    };
    var overlay = new ol.Overlay({
        element: container,
        autoPan: true,
        offset: [0, -10]
    });
    map.addOverlay(overlay);

    var fullscreen = new ol.control.FullScreen();
    map.addControl(fullscreen);

    map.on('click', function(evt){
        var feature = map.forEachFeatureAtPixel(evt.pixel,
          function(feature, layer) {
            return feature;
          });
        if (feature) {
            var geometry = feature.getGeometry();
            var coord = geometry.getCoordinates();
            var content = '<h3>' + feature.get('nama') + '</h3>';
            content += '<img src="assets/img/'+ feature.get('id') +'.jpg" alt="spbu" class="gambarnya">'
            content += '<h5> Alamat : ' + feature.get('alamat') + '</h5>';
            content += '<h5> Jam Kerja : ' + '<a href="http://'+ feature.get('jamkerja') +'" target="_blank">' + feature.get('jamkerja') + '</a></h5>';
            content += '<h5> Rating : ' + feature.get('Rating') + '</h5>';
            content_element.innerHTML = content;
            overlay.setPosition(coord);   
            console.info(feature.getProperties());
        }
    });
    map.on('pointermove', function(evt) {
        if (e.dragging) return;
        var pixel = map.getEventPixel(e.originalEvent);
        var hit = map.hasFeatureAtPixel(pixel);
        map.getTarget().style.cursor = hit ? 'pointer' : '';
    });
    </script>
  </body>
</html>