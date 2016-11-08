/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var image = [];
//    image['stores'] = '../templates/protostar/images/SPRINGFIELD.png';
//    image['installers'] = 'markerinstaller.png';
var map;
var lat = '11.1816492';
var long = '75.8905998';
var myCenter = new google.maps.LatLng(lat, long);
var mapDatas = [{"lat": "11.22512", "long": "75.83635", "address": "14/409/G4,"+"<br/>"+
 "Al Amana Tower ,"+"<br/>"+
 "Olavanna Road,"+"<br/>"+
 "Kunnathupalam,"+"<br/>"+ 
 "Kozhikode, Kerala 673019", "email": "contact@storieshomes.com", "tel_no": "+91 495 243 5666 ", "mob": "+91 8589065666", "web_address": "", "city": "Calicut", "brand_name": "STORIES"}, {"lat": "11.2313419", "long": "75.8515453", "address": " Airport By Pass Road ,<br/> Pantheerankav Junction,<br/> Kozhikode, Kerala 673019 ", "email": "", "tel_no": "+91 495 2433633", "mob": "+91 8589065666", "web_address": "", "city": "Calicut", "brand_name": "STORIES"}];
var markers = [];
function initialize()
{
    var mapProp = {
        zoom: 11,
        center: myCenter,
        panControl: true,
        zoomControl: true,
        mapTypeControl: true,
        scaleControl: true,
        streetViewControl: true,
        overviewMapControl: true,
        rotateControl: true
    }
//   
//        var mapProp = {
//            center: myCenter,
//            zoom: 8,
//            mapTypeId: google.maps.MapTypeId.ROADMAP
//        };
    map = new google.maps.Map(document.getElementById("googleMap"), mapProp);
    var infowindow = new google.maps.InfoWindow();
    var marker, i;

    if (mapDatas.length > 0) {
        map.setCenter(new google.maps.LatLng(mapDatas[0].lat, mapDatas[0].long));
    }
    else {
        alert('No data found');
    }

    for (i = 0; i < mapDatas.length; i++) {

//            var myCenter = new google.maps.LatLng(mapDatas[i].lat, mapDatas[i].long);
        var markerImage = mapDatas[i].brand_name;
        marker = new google.maps.Marker({
            position: new google.maps.LatLng(mapDatas[i].lat, mapDatas[i].long),
            //  icon: '/stoies_megento_dev/skin/frontend/rwd/default/images/logo_small.png',
            map: map,
//                title: mapDatas[i].address
        });
        marker.setMap(map);

        markers.push(marker);


        google.maps.event.addListener(marker, 'click', (function(marker, i) {
            return function() {
                var ContmarkerImage = mapDatas[i].brand_name;
                var email = mapDatas[i].email;
                var address = mapDatas[i].address;
                var tel_no = mapDatas[i].tel_no;
                var mob = mapDatas[i].mob;
                var web_address = mapDatas[i].web_address;

                if ((email == '') || (email == null)) {
                    contEmail = '';
                }
                else {
                    contEmail = '<b>Email: </b>' + email;

                }
                if ((address == '') || (address == null)) {
                    contAdress = '';
                }
                else {
                    contAdress = '' + address;

                }
                if ((web_address == '') || (web_address == null)) {
                    contWeb = '';
                }
                else {
                    contWeb = '<b>Web Adress: </b>' + web_address;

                }

                if ((tel_no == '') || (tel_no == null)) {
                    contPhone = '';
                }
                else {
                    contPhone = '<b>Phone: </b>' + tel_no;

                }
                if ((mob == '') || (mob == null)) {
                    contMobile = '';
                }
                else {
                    contMobile = '<b>Mobile: </b>' + mob;

                }
                var infoContent = '<div class="infoContent"><div style="background:url(\'/skin/frontend/rwd/default/images/logo_small.png\') no-repeat left; padding-left:45px; height:40px; width: z-index:999; "></div>\n\
                      ' + contAdress + ' <br>\n\
                      ' + contPhone + ' <br>\n\
                      ' + contMobile + ' <br>\n\
                      ' + contEmail + ' <br>\n\
                      ' + web_address + '</div>'

                infowindow.setContent(infoContent);

                infowindow.open(map, marker);
            }

        })
                (marker, i));
//            marker.set('i', markers.length);

    }


}

google.maps.event.addDomListener(window, 'load', initialize);
