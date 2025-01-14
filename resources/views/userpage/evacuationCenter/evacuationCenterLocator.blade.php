<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('partials.headPackage')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.dataTables.min.css">
</head>

<body>
    <div class="wrapper">
        @include('partials.header')
        @include('partials.sidebar')
        <div class="main-content">
            <div class="label-container">
                <div class="icon-container">
                    <div class="icon-content">
                        <i class="bi bi-search"></i>
                    </div>
                </div>
                <span>EVACUATION CENTER LOCATOR</span>
            </div>
            <hr>
            <div class="locator-content">
                <div class="locator-header">
                    <div class="header-title"><span>Cabuyao City Map</span></div>
                </div>
                <div class="map-section">
                    <div class="locator-map" id="map"></div>
                </div>
            </div>
            <div class="evacuation-button-container">
                <div class="evacuation-markers">
                    <div class="markers-header">
                        <p>Markers</p>
                    </div>
                    <div class="marker-container">
                        <div class="markers">
                            <div class="marker-count-container active">
                                <div class="marker-count active">0</div>
                            </div>
                            <img src="{{ asset('assets/img/Active.png') }}" alt="Icon">
                            <span class="fw-bold">Active Evacuation</span>
                        </div>
                        <div class="markers">
                            <div class="marker-count-container inactive">
                                <div class="marker-count inactive">0</div>
                            </div>
                            <img src="{{ asset('assets/img/Inactive.png') }}" alt="Icon">
                            <span class="fw-bold">Inactive Evacuation</span>
                        </div>
                        <div class="markers">
                            <div class="marker-count-container full">
                                <div class="marker-count full">0</div>
                            </div>
                            <img src="{{ asset('assets/img/Full.png') }}" alt="Icon">
                            <span class="fw-bold">Full Evacuation</span>
                        </div>
                        <div class="markers" id="flood-marker">
                            <div class="marker-count-container flooded">
                                <div class="marker-count flooded">0</div>
                            </div>
                            <img src="{{ asset('assets/img/Flooded.png') }}" alt="Icon">
                            <span class="fw-bold">Flooded Area</span>
                        </div>
                        <div class="markers" id="roadblocked-marker">
                            <div class="marker-count-container roadblocked">
                                <div class="marker-count roadblocked">0</div>
                            </div>
                            <img src="{{ asset('assets/img/Roadblocked.png') }}" alt="Icon">
                            <span class="fw-bold">Roadblocked</span>
                        </div>
                        <div class="markers" id="user-marker" hidden>
                            <img src="{{ asset('assets/img/User.png') }}" alt="Icon">
                            <span class="fw-bold">You</span>
                        </div>
                    </div>
                </div>
                <div class="locator-button-container">
                    <button type="button" id="locateNearestBtn"
                        {{ $onGoingDisasters->isEmpty() ? 'hidden' : 'disabled' }}>
                        <i class="bi bi-search"></i>Locate Nearest Active Evacuation</button>
                    <button type="button" id="pinpointCurrentLocationBtn">
                        <i class="bi bi-geo"></i>Pinpoint Current Location</button>
                </div>
            </div>
            <div class="table-container">
                <div class="table-content">
                    <header class="table-label">List of Evacuation Centers</header>
                    <table class="table" id="evacuationCenterTable" width="100%">
                        <thead>
                            <tr>
                                <th colspan="2">Name</th>
                                <th>Barangay</th>
                                <th>Latitude</th>
                                <th>Longitude</th>
                                <th>No. of Evacuee</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
            @include('userpage.evacuationCenter.facilitiesModal')
            @include('userpage.evacuationCenter.feedbackForm')
        </div>
        @include('userpage.changePasswordModal')
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"
        integrity="sha512-rstIgDs0xPgmG6RX1Aba4KV5cWJbAMcvRCVmglpam9SoHZiUCyQVDdH2LPlxoHtrv17XWblE/V/PP+Tr04hbtA=="
        crossorigin="anonymous"></script>
    @include('partials.script')
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script
        src="https://maps.googleapis.com/maps/api/js?key={{ config('services.googleMap.key') }}&callback=initMap&v=weekly"
        defer></script>
    <script src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous">
    </script>
    @include('partials.toastr')
    <script>
        let map, activeInfoWindow, userMarker, userBounds, directionDisplay, evacuationCentersData, rowData,
            prevNearestEvacuationCenter, evacuationCenterTable, findNearestActive, reportMarker, reportWindow, radius,
            watchId = null,
            locating = false,
            pinClicked = false,
            routeDisplayed = false,
            reportSubmitting = false,
            geolocationBlocked = false,
            reportButtonClicked = false,
            hasActiveEvacuationCenter = false,
            evacuationCenterJson = [],
            evacuationCenterMarkers = [],
            areaMarkers = [],
            activeEvacuationCenters = [];

        const markerCount = {
                active: $('.marker-count.active'),
                inactive: $('.marker-count.inactive'),
                full: $('.marker-count.full'),
                flooded: $('.marker-count.flooded'),
                roadblocked: $('.marker-count.roadblocked')
            },
            options = (timeout = Infinity) => ({
                enableHighAccuracy: true,
                timeout: timeout,
                maximumAge: 0
            }),
            errorCallback = (navigatorId, error) => {
                switch (error.code) {
                    case error.PERMISSION_DENIED:
                        showWarningMessage(
                            'Request for geolocation denied. To use this feature, please allow the browser to locate you.'
                        );
                        $('#locateNearestBtn').removeAttr('disabled');
                        locating = false;
                        geolocationBlocked = true;
                        break;
                    case error.TIMEOUT:
                    case error.POSITION_UNAVAILABLE:
                        if (!routeDisplayed) {
                            locating = false;
                            showWarningMessage('Cannot get your current location.');
                        }
                        break;
                }

                pinClicked = false;
                $('#loader').removeClass('show');
                $('#reportAreaBtn').prop('hidden', 0);
                if (navigatorId || (!navigatorId && routeDisplayed)) navigator.geolocation.clearWatch(navigatorId);
            };

        function initMap() {
            map = new google.maps.Map(document.getElementById("map"), {
                center: {
                    lat: 14.246261,
                    lng: 121.12772
                },
                zoom: 13,
                zoomControl: false,
                mapTypeControlOptions: {
                    style: google.maps.MapTypeControlStyle.DROPDOWN_MENU
                }
            });
            directionDisplay = new google.maps.DirectionsRenderer({
                map,
                suppressMarkers: true,
                preserveViewport: true,
                markerOptions: {
                    icon: {
                        url: "{{ asset('assets/img/User.png') }}",
                        scaledSize: new google.maps.Size(35, 35)
                    }
                }
            });

            map.controls[google.maps.ControlPosition.TOP_RIGHT].push(Object.assign(document.createElement('div'), {
                className: 'stop-btn-container',
                innerHTML: `<button id="stopLocatingBtn" class="btn-remove"><i class="bi bi-stop-circle"></i>Stop Locating</button>`
            }));
            map.controls[google.maps.ControlPosition.CENTER].push(Object.assign(document.createElement('div'), {
                id: 'loader',
                innerHTML: `<div id="loader-inner"></div><div id="loading-text"></div>`
            }));

            if ('{{ $prefix }}' == 'resident')
                map.controls[google.maps.ControlPosition.BOTTOM_RIGHT].push(Object.assign(document.createElement('div'), {
                    className: 'report-btn-container',
                    innerHTML: `<button id="reportAreaBtn" class="btn-update"><i class="bi bi-megaphone"></i>Report Area</button>`
                }));
        }

        function initMarkers(markersData, type, markersArray) {
            while (markersArray.length) markersArray.pop().setMap(null);

            markersData.forEach((data) => {
                let picture = type == "evacuationCenter" ? data.status : data.type,
                    marker = generateMarker({
                        lat: parseFloat(data.latitude),
                        lng: parseFloat(data.longitude)
                    }, "{{ asset('assets/img/picture.png') }}".replace('picture', picture));

                markersArray.push(marker);

                let content = `
                    <div class="areaReportContainer">
                        ${type == "evacuationCenter" ?
                        `<div class="info-description">
                            <span>Name:</span> ${data.name}
                        </div>
                        <div class="info-description">
                            <span>Barangay:</span> ${data.barangay_name}
                        </div>
                        <div class="info-description">
                            <span>No. of evacuees:</span> ${data.evacuees}
                        </div>
                        <div class="info-description status">
                            <span>Status:</span>
                            <span class="status-content bg-${getStatusColor(data.status)}">
                                ${data.status}
                            </span>
                        </div>` :
                        `<div class="info-description">
                            <span>Report Date:</span> ${formatDateTime(data.report_time)}
                        </div>
                        <div class="info-description">
                            <span>${data.type == "Flooded" ? `${data.type} Area` : data.type}</span>
                        </div>
                        <div class="info-description details">
                            <span>Details: </span>
                            <div class="info-window-details-container">
                                ${data.details}
                            </div>
                        </div>
                        <div class="info-description photo">
                            <span>Image: </span>
                            <div hidden>
                                ${data.latitude}, ${data.longitude}
                            </div>
                            <button class="btn btn-sm btn-primary toggleImageBtn">
                                <i class="bi bi-chevron-expand"></i>View
                            </button>
                            <img src="/reports_image/${data.photo}" class="form-control" hidden>
                        </div>
                        <div class="info-description update" ${data.update.length == 0 ? 'hidden' : ''}>
                            <span>Updates Today: </span>
                            <div class="info-window-update-container">
                                <div class="update-date">
                                    ${data.update.length > 0 ? formatDateTime(data.update[0].update_time, 'date') : ''}
                                </div>
                                ${data.update.length > 0 ?
                                    data.update.map((update) => {
                                    return `<p class="update-details-container">
                                        <small>
                                            as of ${formatDateTime(update.update_time, 'time')}
                                        </small><br>
                                        <span class="update-details">
                                            ${update.update_details}
                                        </span>
                                    </p>`}).join('') : ''
                                }
                            </div>
                        </div>`}
                    </div>`;
                generateInfoWindow(marker, content);
            });
        }

        function generateInfoWindow(marker, content) {
            if (!locating) closeInfoWindow();

            const infoWindow = new google.maps.InfoWindow({
                content
            });

            marker.addListener('click', () => {
                if (!marker.icon.url.includes('Reporting')) {
                    closeInfoWindow();
                    activeInfoWindow = infoWindow;
                }

                if (marker.icon.url.includes('User'))
                    zoomToUserLocation();

                openInfoWindow(infoWindow, marker);
            });

            if (marker.icon.url.includes('Reporting')) {
                reportMarker = marker;
                reportWindow = infoWindow;
                openInfoWindow(infoWindow, marker);
            }
        }

        function generateMarker(position, icon, draggable = false, reportLabel = null) {
            return new google.maps.Marker({
                position,
                map,
                draggable,
                icon: {
                    url: icon,
                    scaledSize: new google.maps.Size(35, 35)
                },
                label: reportLabel
            });
        }

        function generateCircle(center) {
            const color = localStorage.getItem('theme') == 'dark' ? "#ffffff" : "#557ed8";

            radius = new google.maps.Circle({
                map,
                center,
                radius: 50,
                fillColor: color,
                fillOpacity: 0.3,
                strokeColor: color,
                strokeOpacity: 0.8,
                strokeWeight: 2
            });

            return radius;
        }

        function openInfoWindow(infoWindow, marker) {
            infoWindow.open(map, marker);
        }

        function request(origin, destination) {
            return {
                origin,
                destination,
                travelMode: google.maps.TravelMode.WALKING
            };
        }

        function getStatusColor(status) {
            return status == 'Active' ? 'success' : status == 'Inactive' ? 'danger' : 'warning';
        }

        function getKilometers(response) {
            return (response.routes[0].legs[0].distance.value / 1000).toFixed(2);
        }

        function newLatLng(lat, lng) {
            return new google.maps.LatLng(lat, lng);
        }

        function scrollMarkers() {
            $('#user-marker').prop('hidden', 0);
            $('.marker-container').animate({
                scrollLeft: $('#user-marker').position().left + $('.marker-container').scrollLeft()
            }, 500);
        }

        function zoomToUserLocation() {
            map.panTo(userMarker.getPosition());
            map.setZoom(18);
        }

        function closeInfoWindow() {
            activeInfoWindow?.close();
        }

        function getUserLocation() {
            return new Promise((resolve, reject) => {
                if (!navigator.geolocation) {
                    showInfoMessage('Geolocation is not supported by this browser.');
                    $('#locateNearestBtn').removeAttr('disabled');
                    return;
                }

                let currentWatchID, attempt = 0;

                currentWatchID = navigator.geolocation.watchPosition((position) => {
                    if (position.coords.accuracy <= 500) {
                        navigator.geolocation.clearWatch(currentWatchID);
                        geolocationBlocked = false;
                        resolve(position);
                    } else {
                        attempt = attempt + 1;

                        if (attempt == 2)
                            setTimeout(() => {
                                pinClicked = false;
                                $('#loader').removeClass('show');
                                $('#reportAreaBtn').prop('hidden', 0);
                                navigator.geolocation.clearWatch(currentWatchID);
                                showWarningMessage('Cannot get your current location.');
                                resolve(-1);
                            }, 5000);
                    }
                }, (error) => (errorCallback(currentWatchID, error), resolve(-1)), options(5000));
            });
        }

        function setMarker(userlocation) {
            userMarker ?
                (userMarker.setMap(map),
                    userBounds.setMap(map),
                    userMarker.setPosition(userlocation),
                    userBounds.setCenter(userMarker.getPosition())) :
                (userMarker = generateMarker(userlocation,
                        "{{ asset('assets/img/User.png') }}"),
                    userBounds = generateCircle(userMarker.getPosition()));
        }

        function stickMarkerToRadius(position, userBound) {
            let center = userBound.getCenter();

            return google.maps.geometry.spherical.computeOffset(center, userBound.getRadius(),
                google.maps.geometry.spherical.computeHeading(center, position));
        }

        function checkReportLocation(location, userBound) {
            return google.maps.geometry.spherical.computeDistanceBetween(userBound.getCenter(), location) <=
                userBound.getRadius();
        }

        function reportEvent() {
            map.setOptions({
                draggableCursor: 'pointer',
                clickableIcons: false
            });
            showInfoMessage(
                'Click on the map to pinpoint the area. You can drag the marker to adjust location. Click the button again to cancel.'
            );
            $('#reportAreaBtn').html(
                '<i class="bi bi-stop-circle"></i>Cancel Reporting'
            ).addClass('btn-remove');
            google.maps.event.addListener(radius, 'click', (e) => {
                if (reportButtonClicked) {
                    if (reportSubmitting) return;

                    let coordinates = e.latLng;

                    if (reportMarker) {
                        reportMarker.setPosition(coordinates);
                        openInfoWindow(reportWindow, reportMarker);
                        $('[name="latitude"]').val(coordinates.lat());
                        $('[name="longitude"]').val(coordinates.lng());
                    } else {
                        let lastValidPosition;

                        generateInfoWindow(
                            generateMarker(
                                coordinates,
                                "{{ asset('assets/img/Reporting.png') }}",
                                true, {
                                    text: 'Report Location',
                                    className: 'report-marker-label'
                                }
                            ),
                            `<form id="reportAreaForm">
                                @csrf
                                <input type="text" name="latitude" value="${coordinates.lat()}" hidden>
                                <input type="text" name="longitude" value="${coordinates.lng()}" hidden>
                                <div id="reportAreaFormContainer">
                                    <label>Report Type</label>
                                    <select name="type" class="form-select">
                                        <option value="" hidden selected disabled>Select Report Type</option>
                                        <option value="Flooded">Flooded</option>
                                        <option value="Roadblocked">Roadblocked</option>
                                    </select>
                                    <div class="mt-2">
                                        <label>Details</label>
                                        <textarea type="text" name="details" class="form-control" cols="50" rows="10"></textarea>
                                    </div>
                                    <div class="mt-2">
                                        <label>Image</label>
                                        <input type="file" name="image" class="form-control" id="inputImage" accept=".jpeg, .jpg, .png" hidden>
                                        <div class="info-window-action-container report-area">
                                            <button class="btn btn-sm btn-primary" id="imageBtn">
                                                <i class="bi bi-image"></i>Select
                                            </button>
                                        </div>
                                        <img id="selectedReportImage" src="" class="form-control" hidden>
                                        <span id="image-error" class="error" hidden>Please select an image file.</span>
                                    </div>
                                    <center>
                                        <button id="submitAreaBtn" class="modalBtn">
                                            <div id="defaultBtnText">
                                                <i class="bi bi-send"></i>
                                                Submit
                                            </div>
                                            <div id="loadingBtnText" hidden>
                                                <div id="btn-loader">
                                                    <div id="loader-inner"></div>
                                                </div>
                                                Submitting
                                            </div>
                                        </button>
                                    <center>
                                </div>
                            </form>`
                        );
                        reportMarker.addListener('drag', (e) => {
                            let newPosition = e.latLng;

                            if (checkReportLocation(newPosition, userBounds))
                                lastValidPosition = newPosition;

                            reportWindow.close();
                            reportMarker.setPosition(checkReportLocation(newPosition,
                                userBounds) ? newPosition : stickMarkerToRadius(
                                newPosition, userBounds));
                        });
                        reportMarker.addListener('dragend', () => {
                            let newPosition = reportMarker.getPosition(),
                                snappedPosition = stickMarkerToRadius(newPosition, userBounds);

                            if (!checkReportLocation(newPosition, userBounds)) {
                                reportMarker.setPosition(snappedPosition);
                                openInfoWindow(reportWindow, reportMarker);
                                $('[name="latitude"]').val(snappedPosition.lat());
                                $('[name="longitude"]').val(snappedPosition.lng());
                            } else {
                                openInfoWindow(reportWindow, reportMarker);
                                $('[name="latitude"]').val(lastValidPosition.lat());
                                $('[name="longitude"]').val(lastValidPosition.lng());
                            }
                        });
                    }
                }
            });
            google.maps.event.addListener(map, 'click', (e) => {
                if (!checkReportLocation(e.latLng, userBounds)) return;
            });
        }

        async function getEvacuationCentersDistance() {
            $('#locateNearestBtn').attr('disabled', 1);
            evacuationCenterJson.length = 0;
            activeEvacuationCenters.length = 0;

            for (const data of evacuationCentersData) {
                if (data.status != 'Inactive') {
                    activeEvacuationCenters.push(data);
                }
            }

            if (activeEvacuationCenters.length == 0) {
                hasActiveEvacuationCenter = false;
                if (locating && findNearestActive) {
                    $('#stopLocatingBtn').click();
                    $('#loader').removeClass('show');
                    $('#reportAreaBtn').prop('hidden', 0);
                    showWarningMessage('There are currently no active evacuation centers.');
                }
            } else {
                hasActiveEvacuationCenter = true;
                if (!geolocationBlocked) {
                    const position = await getUserLocation();

                    if (position != -1) {
                        const promises = activeEvacuationCenters.map(data => {
                            return new Promise(resolve => {
                                const direction = new google.maps.DirectionsService();
                                direction.route(request(
                                        newLatLng(position.coords.latitude, position.coords
                                            .longitude),
                                        newLatLng(data.latitude, data.longitude)),
                                    (response, status) => {
                                        if (status == 'OK') {
                                            evacuationCenterJson.push({
                                                id: data.id,
                                                status: data.status,
                                                latitude: data.latitude,
                                                longitude: data.longitude,
                                                distance: parseFloat(getKilometers(
                                                    response))
                                            });
                                            resolve();
                                        }
                                    }
                                );
                            });
                        });

                        await Promise.all(promises);
                        if (evacuationCenterJson.length > 1) {
                            const unique = new Set();
                            evacuationCenterJson = evacuationCenterJson
                                .filter(({
                                    id,
                                    latitude,
                                    longitude
                                }) => {
                                    const identifier = `${id}-${latitude}-${longitude}`;
                                    return unique.has(identifier) ? false : unique.add(identifier);
                                })
                                .sort((a, b) => a.distance - b.distance);
                        }
                    }
                }
            }

            $('#locateNearestBtn').removeAttr('disabled');
        }

        function locateEvacuationCenter() {
            let status = false,
                attempt = 0;

            watchId = navigator.geolocation.watchPosition(async (position) => {
                if (position.coords.accuracy <= 500) {
                    status = true;
                    geolocationBlocked = false;

                    if (findNearestActive && evacuationCenterJson.length == 0) {
                        await getEvacuationCentersDistance();
                        if (!hasActiveEvacuationCenter || evacuationCenterJson.length == 0) {
                            $('#stopLocatingBtn').click();
                            navigator.geolocation.clearWatch(watchId);
                            return;
                        }
                    }

                    const {
                        latitude,
                        longitude
                    } = findNearestActive ?
                        evacuationCenterJson[0] : rowData,
                        directionService = new google.maps.DirectionsService();

                    directionService.route(
                        request(
                            newLatLng(position.coords.latitude, position.coords.longitude),
                            newLatLng(latitude, longitude)
                        ),
                        function(response, status) {
                            if (status == 'OK' && locating) {
                                setMarker(response.routes[0].legs[0].start_location);
                                generateInfoWindow(
                                    userMarker,
                                    `<div class="info-window-container">
                                        <center>You are here.</center>
                                        <center class="info-description">
                                            <span>Pathway distance to evacuation: </span>
                                            ${getKilometers(response)} km
                                        </center>
                                    </div>`
                                );

                                if ($('.stop-btn-container').is(':hidden')) {
                                    routeDisplayed = true;
                                    $('#reportAreaBtn').prop('hidden', 0);
                                    $('#loader').removeClass('show');
                                    directionDisplay.setMap(map);

                                    let bounds = new google.maps.LatLngBounds();

                                    response.routes[0].legs.forEach(({
                                            steps
                                        }) =>
                                        steps.forEach(({
                                                start_location,
                                                end_location
                                            }) =>
                                            (bounds.extend(start_location), bounds.extend(
                                                end_location))
                                        )
                                    );
                                    map.fitBounds(bounds);
                                    $('.stop-btn-container').show();
                                    scrollMarkers();
                                }

                                directionDisplay.setDirections(response);

                                if (findNearestActive)
                                    prevNearestEvacuationCenter = evacuationCenterJson[0];
                            }
                        }
                    );
                } else {
                    if (!routeDisplayed) {
                        status = false;
                        attempt = attempt + 1;

                        if (attempt == 2)
                            setTimeout(() => {
                                if (!status) {
                                    if (!routeDisplayed) {
                                        showWarningMessage('Cannot get your current location.');
                                        navigator.geolocation.clearWatch(watchId);
                                    }
                                    $('#loader').removeClass('show');
                                    $('#stopLocatingBtn').click();
                                }
                            }, 5000);
                    }
                }
            }, (error) => errorCallback(null, error), options());
        }

        function ajaxRequest(type = "evacuationCenter") {
            let url = type == "reportArea" ?
                '{{ $prefix }}' == 'resident' ?
                "{{ route('resident.area.get', ['locator', 'null', 'null']) }}" :
                ('{{ $prefix }}' == 'cswd' ?
                    "{{ route('cswd.area.get', ['locator', 'null', 'null']) }}" :
                    "{{ route('area.get', ['locator', 'null', 'null']) }}") :
                '{{ $prefix }}' == 'resident' ?
                "{{ route('resident.evacuation.center.get', ['locator', 'active']) }}" :
                ('{{ $prefix }}' == 'cswd' ?
                    "{{ route('evacuation.center.get', ['locator', 'active']) }}" :
                    "{{ route('cdrrmo.evacuation.center.get', ['locator', 'active']) }}");

            return new Promise((resolve, reject) => {
                $.ajax({
                    method: 'GET',
                    url: url,
                    success(response) {
                        let data = response;

                        if (type == "evacuationCenter") {
                            data = data.data;
                            evacuationCentersData = data;
                            '{{ !$onGoingDisasters->isEmpty() }}' && getEvacuationCentersDistance();
                            let count = {
                                active: 0,
                                inactive: 0,
                                full: 0
                            };

                            evacuationCentersData.forEach((data) => {
                                data.status == "Active" ?
                                    count.active++ : data.status == "Inactive" ?
                                    count.inactive++ : count.full++;
                            });

                            for (const key in count)
                                markerCount[key].text(count[key]);
                        } else {
                            let count = {
                                flooded: 0,
                                roadblocked: 0
                            };

                            data.forEach((data) => {
                                data.type == "Flooded" ?
                                    count.flooded++ : count.roadblocked++;
                            });

                            for (const key in count)
                                markerCount[key].text(count[key]);
                        }

                        initMarkers(data, type, type == "evacuationCenter" ?
                            evacuationCenterMarkers : areaMarkers);
                        resolve();
                    }
                });
            });
        }

        $(document).ready(() => {
            ajaxRequest('reportArea');
            ajaxRequest().then(() => {
                evacuationCenterTable = $('#evacuationCenterTable').DataTable({
                    ordering: false,
                    responsive: true,
                    data: evacuationCentersData,
                    columns: [{
                            data: 'id',
                            name: 'id',
                            visible: false
                        },
                        {
                            data: 'name',
                            name: 'name'
                        },
                        {
                            data: 'barangay_name',
                            name: 'barangay_name'
                        },
                        {
                            data: 'latitude',
                            name: 'latitude',
                            visible: false
                        },
                        {
                            data: 'longitude',
                            name: 'longitude',
                            visible: false
                        },
                        {
                            data: 'evacuees',
                            name: 'evacuees',
                            width: '1rem',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'status',
                            name: 'status',
                            width: '10%'
                        },
                        {
                            data: 'action',
                            width: '1rem',
                            orderable: false,
                            searchable: false
                        }
                    ],
                    columnDefs: [{
                        targets: 6,
                        render(data) {
                            return `<div class="status-container">
                                    <div class="status-content bg-${getStatusColor(data)}">
                                        ${data}
                                    </div>
                                </div>`;
                        }
                    }]
                });
            });

            $('.marker-count.active, .marker-count.inactive, .marker-count.full, .marker-count.flooded, .marker-count.roadblocked')
                .hover(function() {
                    $(this).attr('title', `Total of ${$(this).text()}`);
                });

            $(document).on("click", "#pinpointCurrentLocationBtn", function() {
                if (!locating && !pinClicked && (userMarker == null || !userMarker.getMap())) {
                    if (!geolocationBlocked) {
                        scrollToElement('.locator-content');
                        $('#loader').addClass('show');
                        $('#reportAreaBtn').prop('hidden', 1);
                        $("#loading-text").text("Getting your location...");
                    }
                    pinClicked = true;
                    getUserLocation().then((position) => {
                        if (position != -1) {
                            pinClicked = false;
                            $('#loader').removeClass('show');
                            $('#reportAreaBtn').prop('hidden', 0);
                            setMarker(newLatLng(position.coords.latitude, position.coords
                                .longitude));
                            generateInfoWindow(userMarker,
                                `<div class="info-window-container">
                                        <div class="info-description">
                                            <center>You are here.</center>
                                        </div>
                                    </div>`);
                            scrollToElement('.locator-content');
                            zoomToUserLocation();
                            scrollMarkers();
                        }
                    });
                }
            });

            $(document).on("click", "#locateNearestBtn, .locateEvacuationCenter", function() {
                if (!locating) {
                    if (!geolocationBlocked) {
                        scrollToElement('.locator-content');
                        $("#loading-text").text("Locating evacuation center...");
                        $('#loader').addClass('show');
                        $('#reportAreaBtn').prop('hidden', 1);
                    }
                    findNearestActive = !$(this).hasClass('locateEvacuationCenter');
                    rowData = findNearestActive ? null : getRowData(this, evacuationCenterTable);
                    locating = true;
                    locateEvacuationCenter();
                }
            });

            $(document).on("click", "#stopLocatingBtn", function() {
                locating = false;
                routeDisplayed = false;
                watchId && (navigator.geolocation.clearWatch(watchId));
                directionDisplay?.setMap(null);
                userMarker?.setMap(null);
                userBounds?.setMap(null);
                closeInfoWindow();
                $('.stop-btn-container').hide();
                map.setCenter(newLatLng(14.246261, 121.12772));
                map.setZoom(13);
                $('#user-marker').prop('hidden', 1);
            });

            $(document).on("click", "#reportAreaBtn", function() {
                if (this.textContent == 'Report Area' || !reportButtonClicked) {
                    reportButtonClicked = true;

                    if (locating) {
                        reportEvent();
                    } else {
                        getUserLocation().then((position) => {
                            if (position != -1) {
                                setMarker(newLatLng(position.coords.latitude, position.coords
                                    .longitude));
                                generateInfoWindow(userMarker,
                                    `<div class="info-window-container">
                                        <div class="info-description">
                                            <center>You are here.</center>
                                        </div>
                                    </div>`);
                                scrollToElement('.locator-content');
                                zoomToUserLocation();
                                scrollMarkers();
                                reportEvent();
                            }
                        });
                    }
                } else {
                    map.setOptions({
                        draggableCursor: 'default',
                        clickableIcons: true
                    });
                    reportMarker?.setMap(null);
                    reportMarker = null;
                    $('#reportAreaBtn').html(
                        '<i class="bi bi-megaphone"></i>Report Area'
                    ).removeClass('btn-remove');
                    reportButtonClicked = false;
                    google.maps.event.clearListeners(map, 'click');
                }
            });

            $(document).on('click', '#submitAreaBtn', function() {
                $('#reportAreaForm').validate({
                    rules: {
                        type: 'required',
                        details: 'required'
                    },
                    messages: {
                        type: 'Please select report type.',
                        details: 'Please enter details.'
                    },
                    errorElement: 'span',
                    showErrors() {
                        this.defaultShowErrors();

                        $('#image-error').text('Please select an image.')
                            .prop('style', `display: ${$('#inputImage').val() == '' ?
                                'block' : 'none'} !important`);
                    },
                    submitHandler(form) {
                        if ($('#inputImage').val() == '') return;

                        confirmModal('Are you sure you want to report this area?').then((
                            result) => {
                            if (!result.isConfirmed) return;

                            $.ajax({
                                type: 'POST',
                                url: "{{ route('resident.area.report') }}",
                                data: new FormData(form),
                                contentType: false,
                                processData: false,
                                beforeSend() {
                                    reportSubmitting = true;
                                    $('#defaultBtnText').hide();
                                    $('#loadingBtnText').prop('hidden', 0);
                                    $('select, textarea, #submitAreaBtn, #imageBtn, #reportAreaBtn')
                                        .prop('disabled', 1);
                                },
                                success(response) {
                                    const status = response.status

                                    status == "warning" || status ==
                                        "blocked" ?
                                        showWarningMessage(response
                                            .message) :
                                        showSuccessMessage(
                                            'Report submitted successfully');

                                    status != "warning" && ($('#reportAreaBtn')
                                        .prop('disabled', 0),
                                        $('#reportAreaBtn').click());
                                },
                                error: showErrorMessage,
                                complete() {
                                    reportSubmitting = false;
                                    $('#defaultBtnText').show();
                                    $('#loadingBtnText').prop('hidden', 1);
                                    $('select, textarea, #submitAreaBtn, #imageBtn, #reportAreaBtn')
                                        .prop('disabled', 0);
                                }
                            });
                        });
                    }
                });
            });

            $(document).on('click', '.toggleImageBtn', function() {
                toggleShowImageBtn($(this), $(this).next(), areaMarkers);
            });

            $(document).on("click", ".sendFeedback", function() {
                $('#evacuationId').val(getRowData(this, evacuationCenterTable).id);
                $('#feedbackModal').modal('show');
            });

            $('#sendFeedbackBtn').on('click', function(e) {
                e.preventDefault();

                if ($('.checkbox-container input[type="checkbox"]:checked').length == 0)
                    return $('#feedback-error').text('Please check atleast one option.').prop('hidden', 0);
                else {
                    $('#feedback-error').prop('hidden', 1);
                }

                confirmModal('Do you want to submit this feedback?').then((result) => {
                        if (!result.isConfirmed) return;

                        $.ajax({
                            data: $('#feedbackForm').serialize(),
                            url: "{{ route('resident.evacuation.center.add.feedback') }}",
                            method: "POST",
                            beforeSend() {
                                $('#btn-loader').prop('hidden', 0);
                                $('#btn-text').text('Sending');
                                $('input, #sendFeedbackBtn, #closeModalBtn')
                                    .prop('disabled', 1);
                            },
                            success(response) {
                                $('#btn-loader').removeClass('show');
                                $('#sendFeedbackBtn').prop('disabled', 0);

                                response.status == 'warning' ? showWarningMessage(
                                    response.message) : (
                                    showSuccessMessage(
                                        `Feedback successfully submitted.`
                                    ), $('#feedbackModal').modal('hide'));
                            },
                            error: showErrorMessage,
                            complete() {
                                $('#btn-loader').prop('hidden', 1);
                                $('#btn-text').text('Send');
                                $('input, #sendFeedbackBtn, #closeModalBtn')
                                    .prop('disabled', 0);
                            }
                        });
                    });
            });

            $('#feedbackModal').on('hidden.bs.modal', () => {
                $('.checkbox').prop('checked', false);
            });

            $(document).on('click', '.checkFacilities', function() {
                let {
                    name,
                    facilities
                } = getRowData(this, evacuationCenterTable);

                $('.modalLabelContainer').removeClass('bg-warning');
                $('.modalLabel').text('Facilities List');
                $('.evac-facility-label').text(name);
                $('.facilitiy-label').remove();
                facilities = facilities.split(',');
                facilities.forEach(facility => {
                    $('.facilitiy-list').append(`
                        <div class="facilitiy-label">
                            <i class="bi bi-circle-fill"></i>${facility}
                        </div>
                    `);
                });
                $('#facilitiesModal').modal('show');
            });

            Echo.channel('area-report').listen('AreaReport', () => {
                ajaxRequest('reportArea');
            });

            Echo.channel('disaster').listen('Disaster', (e) => {
                $('#locateNearestBtn').prop({
                    'disabled': e.onGoingDisaster == 0,
                    'hidden': e.onGoingDisaster == 0
                });
            });

            Echo.channel('evacuation-center').listen('EvacuationCenter', () => {
                ajaxRequest().then(() => {
                    if (locating && (rowData != null || prevNearestEvacuationCenter != null)) {
                        const {
                            id,
                            status,
                            latitude,
                            longitude
                        } = findNearestActive ? prevNearestEvacuationCenter : rowData;

                        const isCenterUnavailable = findNearestActive ?
                            !evacuationCentersData.some(evacuationCenter =>
                                evacuationCenter.id == id && ['Active', 'Full'].includes(
                                    evacuationCenter.status)) :
                            !evacuationCentersData.some(evacuationCenter =>
                                evacuationCenter.id == id),

                            isLocationUpdated = !evacuationCentersData.some(
                                evacuationCenter =>
                                evacuationCenter.latitude == latitude &&
                                evacuationCenter.longitude == longitude);

                        if (isCenterUnavailable || isLocationUpdated) {
                            $('#stopLocatingBtn').click();
                            showWarningMessage(
                                isCenterUnavailable ?
                                'The evacuation center you are locating is no longer available.' :
                                'The location of the evacuation center you are locating is updated.'
                            );

                            if (findNearestActive) prevNearestEvacuationCenter = null;
                        }
                    }

                    evacuationCenterTable.clear().rows.add(evacuationCentersData).draw();
                });
            });
        });
    </script>
</body>

</html>
