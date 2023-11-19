<?php require_once ('../config.php'); ?>
<!DOCTYPE html>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>HIP - HYPER Immersive Panorama</title>

    <!-- Include Jquery because it will come in handy.-->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <!-- Include Bootstrap for Styles.-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
    <link href="https://getbootstrap.com/docs/5.2/examples/dashboard/dashboard.css" rel="stylesheet" >

    <!-- Include Bootstrap for Styles.-->
    <script src="https://kit.fontawesome.com/9f85d7915b.js" crossorigin="anonymous"></script>

    <!-- Include Cesium to act as our rendering engine.-->
    <script src="libs/cesium/1.111/Cesium.js"></script>
    <link href="libs/cesium/1.111/Widgets/widgets.css" rel="stylesheet">

    <!-- Include Media Pipe Liabraries to help with Facial Recognition.-->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/@mediapipe/control_utils@0.6/control_utils.css" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="css/face-mesh.css" crossorigin="anonymous">
    <script type="module" src="js/face-mesh.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@mediapipe/camera_utils@0.3/camera_utils.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@mediapipe/control_utils@0.6/control_utils.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@mediapipe/drawing_utils@0.3/drawing_utils.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@mediapipe/face_mesh/face_mesh.js" crossorigin="anonymous"></script>


</head>
<body>


<header class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0 shadow">
    <a class="navbar-brand col-md-6 col-lg-6 me-0 px-3 fs-6" href="#"><i class="fa-solid fa-binoculars"></i> &nbsp; HYPER Immersive Panorama</a>
    <button class="navbar-toggler position-absolute d-md-none collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <input class="form-control form-control-dark rounded-0 border-0" type="text" id="pacViewPlace" name="pacViewPlace" placeholder="Enter a location...">
</header>


<div class="container-fluid h-100">
    <div class="row  h-100">
        <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
            <div class="position-sticky pt-3 sidebar-sticky">
                <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mb-1 text-muted text-uppercase">
                    <span><i class="fa-solid fa-map-pin"></i> &nbsp; Immersive Locations</span>
                </h6>
                <ul class="nav flex-column">
                    <?php foreach($INTERESTING_PLACES as $key => $A_PLACE){ ?>
                    <li class="nav-item">
                        <a class="nav-link" aria-current="page" href="#" onclick="start_place(<?=$key; ?>);">
                            <?=$A_PLACE['label']; ?>
                        </a>
                    </li>
                    <?php }//end loop through interesting places. ?>

                </ul>

                <div class="text-center mt-5 px-3">
                    <button type="button" class="btn btn-dark w-100" data-bs-toggle="modal" data-bs-target="#cameraModal">
                        <i class="fa-solid fa-camera"></i> &nbsp; View Camera Input
                    </button>
                    <a id="place-button" href="#" target="_blank" class="btn btn-success w-100 mt-3" style="display: none">
                        <i class="fa-solid fa-ticket"></i> &nbsp; <span>Visit Place</span>
                    </a>
                    <a href="#" class="btn btn-outline-dark w-100 mt-3" >
                        <i class="fa-brands fa-github"></i> &nbsp; See Code
                    </a>
                </div>
            </div>
        </nav>

        <main class="col-md-9 ms-sm-auto col-lg-10 p-0 h-100">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center h-100">
                <div class="w-100 h-100" id="cesiumContainer"></div>
            </div>
        </main>
    </div>
</div>


<script>

    // Enable simultaneous requests.
    Cesium.Ion.defaultAccessToken = "<?=$CESIUM_AT; ?>";
    Cesium.GoogleMaps.defaultApiKey = "<?=$GOOGLE_MAPS_API_KEY; ?>";
    // Create the viewer and remove unneeded options.
    const viewer = new Cesium.Viewer("cesiumContainer", {
        timeline: false,
        animation: false,
        sceneModePicker: false,
        baseLayerPicker: false,
        homeButton: false,
        navigationHelpButton: false,
        geocoder: false,

        // The globe does not need to be displayed,
        // since the Photorealistic 3D Tiles include terrain
        globe: false,
    });

    // Enable rendering the sky
    viewer.scene.skyAtmosphere.show = true;


    async function loadGoogleTiles(){
        try {
            const tileset = await Cesium.createGooglePhotorealistic3DTileset();
            viewer.scene.primitives.add(tileset);
        } catch (error) {
            console.log(`Error loading Photorealistic 3D Tiles tileset. ${error}`);
        }
    }

    loadGoogleTiles();


    // Point the camera at a location and elevation, at a viewport-appropriate distance.
    function pointCameraAt(location, viewport, elevation) {
        const distance = Cesium.Cartesian3.distance(
            Cesium.Cartesian3.fromDegrees(
                viewport.getSouthWest().lng(), viewport.getSouthWest().lat(), elevation),
            Cesium.Cartesian3.fromDegrees(
                viewport.getNorthEast().lng(), viewport.getNorthEast().lat(), elevation)
        ) / 2;
        const target = new Cesium.Cartesian3.fromDegrees(location.lng(), location.lat(), elevation);
        const pitch = - Math.PI / 4;
        const heading = 0;
        viewer.camera.lookAt(target, new Cesium.HeadingPitchRange(heading, pitch, distance));
    }

    // Rotate the camera around a location and elevation, at a viewport-appropriate distance.
    let unsubscribe = null;
    function rotateCameraAround(location, viewport, elevation) {
        if(unsubscribe) unsubscribe();
        pointCameraAt(location, viewport, elevation);
        unsubscribe = viewer.clock.onTick.addEventListener(() => {
            viewer.camera.rotate(Cesium.Cartesian3.UNIT_Z);
        });
    }

    function initAutocomplete() {
        const autocomplete = new google.maps.places.Autocomplete(
            document.getElementById("pacViewPlace"), {
                fields: [
                    "geometry",
                    "name",
                ],
            }
        );

        autocomplete.addListener("place_changed", async () => {
            const place = autocomplete.getPlace();

            if (!(place.geometry && place.geometry.viewport && place.geometry.location)) {
                window.alert(`Insufficient geometry data for place: ${place.name}`);
                return;
            }
            // Get place elevation using the ElevationService.
            const elevatorService = new google.maps.ElevationService();
            const elevationResponse =  await elevatorService.getElevationForLocations({
                locations: [place.geometry.location],
            });

            if(!(elevationResponse.results && elevationResponse.results.length)){
                window.alert(`Insufficient elevation data for place: ${place.name}`);
                return;
            }
            const elevation = elevationResponse.results[0].elevation || 10;

            rotateCameraAround(
                place.geometry.location,
                place.geometry.viewport,
                elevation
            );
        });
    }

    function set_look(roll, pitch, yaw){

        console.log('setting look');

        const lookFactor = 0.05;
        let roll_ratio = Math.abs(roll) / 30;


        //viewer.camera.setView({
            //orientation: new Cesium.HeadingPitchRoll(-roll*3.14/180, pitch*3.14/180, yaw*3.14/180)
        //});

        //return false;

        if(roll > 3){
            viewer.camera.lookLeft(lookFactor*roll_ratio);
            console.log('looking left');
        }else if(roll < -3){
            viewer.camera.lookRight(lookFactor*roll_ratio);
            console.log('looking right');
        }

        if(pitch > 150){
            let pitch_ratio = Math.abs(pitch -180) / 20;
            viewer.camera.lookUp(lookFactor*pitch_ratio);
            console.log('looking up');
        }else if(pitch < -140 && pitch > -150){
            let pitch_ratio = (Math.abs(pitch) - 150) / 30;
            viewer.camera.lookUp(lookFactor*pitch_ratio);
            console.log('looking down');
        }

        if(yaw > 10){
            //viewer.camera.rotateRight(2 * (3.14/180));
        }

        //viewer.camera.lookUp(y * lookFactor);

    }


</script>




<!-- Modal -->
<div class="modal  fade" id="cameraModal" tabindex="-1" aria-labelledby="cameraModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5 text-dark" id="cameraModalLabel" >Web Camera Settings</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="det-container">
                    <video class="input_video"></video>
                    <div class="canvas-container">
                        <canvas class="output_canvas w-100"  height="300px">
                        </canvas>
                    </div>
                    <div class="loading">
                        <div class="spinner"></div>
                        <div class="message">
                            Loading
                        </div>
                    </div>
                    <script src="https://docs.opencv.org/master/opencv.js"></script>
                </div>
                <div class="control-panel" style="display: none">
                </div>




            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save changes</button>
            </div>
        </div>
    </div>
</div>


<audio controls loop id="audio-player">
    <source src="audio/waterfall.mp3" type="audio/mpeg">
</audio>

<script async src="https://maps.googleapis.com/maps/api/js?key=<?=$GOOGLE_MAPS_API_KEY; ?>&libraries=places&callback=initAutocomplete"></script>


<script type="text/javascript">

    const INTERESTING_PLACES = JSON.parse('<?=json_encode($INTERESTING_PLACES); ?>');

    var audio = document.getElementById("audio-player");

    function start_place(key){

        let place = INTERESTING_PLACES[key];

        console.log('Going to '+place.label);

        if(place.ambient_audio){
            audio.pause();
            audio.src = 'audio/'+place.ambient_audio;
            audio.load();
            setTimeout("audio.play();", 2500);
        }


        viewer.camera.flyTo({
            destination : Cesium.Cartesian3.fromDegrees(place.lon, place.lat, place.height),
            orientation : {
                heading : Cesium.Math.toRadians(place.heading),
                pitch : Cesium.Math.toRadians(0.0),
                roll : 0.0
            }
        });

        if(place.purchase_url){
            $('#place-button').attr('href', place.purchase_url);
            $('#place-button').show();
        }else{
            $('#place-button').hide();
        }


    }

    $( document ).ready(function() {
        

    });


    document.onkeydown = checkKey;

    function checkKey(e) {

        e = e || window.event;

        if (e.keyCode == '38') {
            // up arrow
            viewer.camera.moveForward(1);
        }
        else if (e.keyCode == '40') {
            viewer.camera.moveBackward(1);
        }

    }


</script>


</body>