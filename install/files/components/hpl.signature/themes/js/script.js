$(function() {
    $('#profile').addClass('dragging').removeClass('dragging');
});

$('#profile').on('dragover', function() {
    $('#profile').addClass('dragging')
}).on('dragleave', function() {
    $('#profile').removeClass('dragging')
}).on('drop', function(e) {
    $('#profile').removeClass('dragging hasImage');

    if (e.originalEvent) {
        var file = e.originalEvent.dataTransfer.files[0];
        console.log(file);

        var reader = new FileReader();

        //attach event handlers here...

        reader.readAsDataURL(file);
        reader.onload = function(e) {
            console.log(reader.result);
            $('#profile').css('background-image', 'url(' + reader.result + ')').addClass('hasImage');

        }

    }
})
$('#profile').on('click', function(e) {
    $('#mediaFile').click();
});
window.addEventListener("dragover", function(e) {
    e = e || event;
    e.preventDefault();
}, false);
window.addEventListener("drop", function(e) {
    e = e || event;
    e.preventDefault();
}, false);
$('#mediaFile').change(function(e) {
    var input = e.target;
    if (input.files && input.files[0]) {
        var file = input.files[0];

        var reader = new FileReader();

        reader.readAsDataURL(file);
        reader.onload = function(e) {
            console.log(reader);

            $('.img-show').attr("src", reader.result);
            $('.img_signature').val(reader.result);
        }
    }
})
$(function () {
    $('[data-toggle="tooltip"]').tooltip()
})
function onUpload(input) {
    let originalFile = input.files[0];
    let reader = new FileReader();
    reader.readAsDataURL(originalFile);
    reader.onload = () => {
        let json = JSON.stringify({ dataURL: reader.result });

        // View the file
        let fileURL = JSON.parse(json).dataURL;
        $('.pdf_base64').val(fileURL.replace('data:application/pdf;base64,',''));
        $("#display-pdf").empty();
        $("#display-pdf").append(`<object data="${fileURL}"
            type="application/pdf" width="400px" height="200px">
            </object>`).onload(() => {
            URL.revokeObjectURL(originalFileURL);
        });


    };
}