var last_update = -1;
var toastBootstrap;

$(function() {
    get_data();
    setInterval(get_data, 60000); // Update data every minute

    $("#fileupload").click(function(){
        if ($("#uploadcsv").get(0).files.length === 0){
            alert("Please select a file to upload.");
        } else {
            upload_file();
        }  
    }); 

    toastBootstrap = bootstrap.Toast.getOrCreateInstance($("#liveToast"));
});

function upload_file(){
    $("#fileupload").prop("disabled", true);
    var formData = new FormData();
    formData.append('uploadcsv', $("#uploadcsv").get(0).files[0]);
    $.ajax({
        url: '/call/api',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(data){
            $("#fileupload").prop("disabled", false);
            if (data.success){
                get_data(); // refresh data after posting new data
            } else {
                alert("Error uploading file");
            }
        },
        error: function(){
            $("#fileupload").prop("disabled", false);
            alert("Error uploading file");
        }
    });
}

function get_data(){
    $.ajax({
        url: '/call/api',
        type: 'GET',
        data: {
            last_update: last_update
        },
        success: function(data){
            if (data.last_update !== last_update && data.data){
                last_update = data.last_update;
                let tmp = '';
                data.data.forEach(element => {
                    tmp += '<tr>';
                    tmp += '<td>' + element.customer_id + '</td>';
                    tmp += '<td>' + element.total_calls_internal + '</td>';
                    tmp += '<td>' + element.total_duration_internal + '</td>';
                    tmp += '<td>' + element.total_calls + '</td>';
                    tmp += '<td>' + element.total_duration + '</td>';
                    tmp += '</tr>';
                });
                $("#calldata").html(tmp);
                toastBootstrap.show();
                console.log(toastBootstrap);
            }
        }
    });
}