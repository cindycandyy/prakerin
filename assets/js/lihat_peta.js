function inisialisasi_peta() {
    var gresik = new google.maps.LatLng(-7.163082911727816, 112.63735692948103);
    var petaoption = {
        zoom: 12,
        center: gresik,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    };
    peta = new google.maps.Map(document.getElementById("petaku"), petaoption);
    var marker, i;
    var gmarkers1 = [];
    var infowindow = new google.maps.InfoWindow();

    if (data_poi.length > 0)
    {
        data_poi.forEach(function (data, index) {
            mark_point(data, peta, infowindow, gmarkers1);
        });
    }
}

function mark_point(data, peta, infowindow, gmarkers1) {
    var id = data.id_jenis;
    marker_data = new google.maps.Marker({
        id: data.id_jenis,
        title: data.title,
        position: new google.maps.LatLng(data.latitude, data.longitude),
        map: peta,
        icon: data.gambar_tanda
    });

    gmarkers1.push(marker_data);

    new google.maps.event.addListener(marker_data, 'click', function () {
        var context = viewdetailpelanggaran(data.id);
        infowindow.setContent(context);
        infowindow.open(peta, this);
    });

    filterMarkers = function (category) {
        for (i = 0; i < gmarkers1.length; i++) {
            marker = gmarkers1[i];
            // If is same category or category not picked
            if (marker.id == category || category.length === 0) {
                marker.setVisible(true);
            }
            // Categories don't match 
            else {
                marker.setVisible(false);
            }
        }
    }

}
google.maps.event.addDomListener(window, 'load', inisialisasi_peta);
