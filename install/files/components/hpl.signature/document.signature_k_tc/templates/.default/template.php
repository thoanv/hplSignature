<?php
defined('B_PROLOG_INCLUDED') || die;

use Hpl\Signature\Helper\Helper;
use Bitrix\Main\Error;
use Bitrix\Main\ErrorCollection;

$this->addExternalCss('/local/components/hpl.signature/themes/css/font-awesome.min.css');
$this->addExternalCss('/local/components/hpl.signature/themes/css/bootstrap.min.css');
$this->addExternalCss('/local/components/hpl.signature/themes/css/style.css');
$this->addExternalCss('/local/components/hpl.signature/themes/css/style_signature.css');
$this->addExternalJs('/local/components/hpl.signature/themes/js/jquery-3.1.1.min.js');
$this->addExternalJs('/local/components/hpl.signature/themes/js/bootstrap.min.js');
$this->addExternalJs('/local/components/hpl.signature/themes/js/bootstrap.bundle.min.js');
$this->addExternalJs('/local/components/hpl.signature/themes/js/script.js');
/** @var CBitrixComponentTemplate $this */
$store = $arResult['STORE'];
$userUpload = Helper::getMember($store['created_by']);
$signer = $arResult['SIGNER'];
$method = $signer['method'] ? $signer['method'] : 1;
$date_current = date('d/m/Y');
$dataSigned = $arResult['DATASIGNED'];
/** @var ErrorCollection $errors */
$errors = $arResult['ERRORS'];
if(count($errors)){
?>
<div class="errors-validate alert alert-danger">
    <ul>
    <?
        foreach ($errors as $error) {
            /** @var Error $error */
            ?>
            <li><?ShowError($error->getMessage());?></li>
            <?
        }
        ?>
    </ul>

</div>

<?
}
?>
<form action="<?= POST_FORM_ACTION_URI ?>" method="POST" enctype="multipart/form-data">
    <div class="wrapper template_signatire">
        <div class="sidebar">
            <div class="sidebar-header">
                <a href="javascript:;"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Trình ký</a>
            </div><!-- .sidebar-header -->
            <div class="sidebar-nav">
                <input type="hidden" value="<?=$store['id']?>" class="document_id">
                <div class="mt-4 mb-4">
                    <button type="submit" name="access_token" value="true" class="btn btn-primary insert-signature">
                        <img src="/local/components/hpl.signature/themes/images/signature-pen.png" width="50" alt=icon-pen">
                        <p class="txt-xac-nhan-ky">Xác nhận ký</p>
                    </button>
                </div><!-- .btn-group -->
                <div class="mb-4">
                    <button type="submit" name="download" value="true" class="btn btn-primary insert-signature">Hoàn thành ký</button>
                </div>
                <div class="mb-4">
                    <button type="button" onclick="downloadPDF()" value="true" class="btn btn-primary insert-signature">Downlod File ký</button>
                </div>
                <div class="mb-4">
                    <button class="btn btn-primary insert-signature" onclick="savePdf()">Chèn chữ ký</button>
                </div>
                <div class="btn-group-rounded mt-1 mb-1">
                    <h6>Phương thức ký</h6>
                    <div class="form-check">
                        <input class="form-check-input" onchange="handleChange(this);" type="radio" <?=$method == 1 ? 'checked' : ''?> name="method" id="apostrophe" value="1">
                        <label class="form-check-label" for="apostrophe">
                            Ký nháy
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" onchange="handleChange(this);" type="radio" name="method" <?=$method == 2 ? 'checked' : ''?> id="signTheApplication" value="2">
                        <label class="form-check-label" for="signTheApplication">
                            Ký đơn
                        </label>
                    </div>
                </div><!-- .btn-group -->
                <div class="btn-group-rounded mt-1 mb-1">
                    <h6>Thông tin tài liệu</h6>

                </div><!-- .btn-group -->

            </div><!-- .sidebar-nav -->
        </div><!-- .sidebar -->

        <div class="content">
            <div class="content-header">
                <div class="float-right pl-2"><strong>Page</strong> <span class="page-current" id="pageCurrent">1</span>/<span class="page-of" id="pageOf"></span></div>
                <h2><?=$store['name']?></h2>
            </div><!-- .content-header -->

            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="document-container">
                            <div class="document-render" id="documentRender">
                                <div class="digital-signature" id="digitalSignature">
                                    <img src="<?=$signer['img_signature']?>" class="signature-item">
                                </div><!-- .digital-signature -->
                            </div><!-- .document-render -->
                        </div><!-- .document-container -->
                    </div><!-- .col-## -->
                </div><!-- .row -->
            </div><!-- .container-fluid -->
        </div><!-- .content -->
        <input type="hidden" name="heightValue" id="heightValue" value="71" readonly="readonly">
        <input type="hidden" name="widthValue" id="widthValue" value="150" readonly="readonly">

    </div><!-- .wrapper -->
</form>

<script src="/local/components/hpl.signature/themes/js/pdfjs/pdf.js"></script>
<script src="/local/components/hpl.signature/themes/js/interact/interact.min.js"></script>
<script src="https://unpkg.com/ionicons@4.4.2/dist/ionicons.js"></script>

<script>
    'use strict';
    var BASE64_MARKER = ';base64,';
    function convertDataBase64ToBinary (dataBase64) {
        var base64Index = dataBase64.indexOf(BASE64_MARKER) + BASE64_MARKER.length;
        var base64 = dataBase64.substring(base64Index);
        var raw = window.atob(base64);
        var rawLength = raw.length;
        var array = new Uint8Array(new ArrayBuffer(rawLength));

        for(var i = 0; i < rawLength; i++) {
            array[i] = raw.charCodeAt(i);
        }
        return array;
    }

    function renderPDF (url, canvasContainer, options) {
        var pdfjs = window['pdfjs-dist/build/pdf']
        var options = options || { scale: 1 }

        function renderPage(page) {
            var viewport = page.getViewport(options.scale);
            var canvas = document.createElement('canvas');
            var contex = canvas.getContext('2d');
            var renderContext = {
                canvasContext: contex,
                viewport: viewport
            };

            canvas.height = viewport.height;
            canvas.width = viewport.width;
            canvasContainer.appendChild(canvas);

            page.render(renderContext);
        }

        function pageNumber(pdfDoc) {
            for(var page = 1; page <= pdfDoc.numPages; page++)
                pdfDoc.getPage(page).then(renderPage);
            document.getElementById('pageOf').innerHTML += pdfDoc.numPages
        }

        url = convertDataBase64ToBinary(url)
        var setPDF = pdfjs.getDocument(url);
        setPDF.then(pageNumber)
    }

    let pdfData = 'data:application/pdf;base64,<?=$store['file']?>';
    renderPDF(pdfData, document.getElementById('documentRender'), { scale: 1.55 });
    function downloadPDF() {
        const linkSource = `${pdfData}`;
        const downloadLink = document.createElement("a");
        const fileName = "abc.pdf";
        downloadLink.href = linkSource;
        downloadLink.download = fileName;
        downloadLink.click();}
</script>
<script src="/local/components/hpl.signature/themes/js/signature.config.js"></script>
<script>
    $(window).scroll(function() {
        var scroll = $(window).scrollTop();
        if (scroll >= 0.4 * $(window).height()) {
            $('.template_signatire .sidebar').css({'position':'fixed', 'top': 0})
            $('.content-header').css({'position':'fixed', 'top': 0, 'width': '82%'})
        } else {
            $('.template_signatire .sidebar').css({'position':'absolute', 'top': 'unset'})
            $('.content-header').css({'position':'unset', 'top': 'unset', 'width': '100%'})

        }
    });
    function handleChange(src) {
        let check = src.value;
        if(check == 1){
            $('.info-signature').css('display', 'none')
        }else{
            $('.info-signature').css('display', 'block')

        }
    }
    function savePdf(){
        var windowsize = $(window).width();
        let p = $('#pageCurrent').text();
        let x = $('.digital-signature').attr('data-x');
        let y = $('.digital-signature').attr('data-y');
        let w = $('#widthValue').val();
        let h = $('#heightValue').val();

        if(windowsize >= 1598 && windowsize<=1903){
            x = Number(x)-160;
        }else if(windowsize >= 1425 && windowsize<1598){
            x = Number(x)-73;
        }else if(windowsize>1903){
            x = Number(x)-265;
        }
        console.log(x);
        let id = <?=$store['id']?>;
        let signature_id = <?=$signer['id']?>;
        if(!id){
            alert('Không xác định đối tượng');
            window.location.reload();
            return;
        }
        console.log(x);
        if(isNaN(x) || x<=-1){
            alert('Vui lòng kéo chữ ký vào trong khung tài liệu');
            return;
        }
        let r = $('#xValue').val();
        let c = $('#yValue').val();
        $.ajax({
            type: 'POST',
            url: '/local/components/hpl.signature/document.signature/templates/.default/ajax.php',
            data: {
                document_id: id,
                signature_id: signature_id,
                x: Math.round(x),
                y: Math.round(y),
                w: Math.round(w),
                h: Math.round(h),
                p: Number(p),
                ajax_action: 'savefilePdf'
            },
            dataType: 'json',
            success: function (response) {
                console.log(response)
                if (response.SUCCESS == true) {
                    alert('Copy tiêu chí thành công');
                    window.location.reload();

                }
            }
        });
    }
</script>
<!--<script src="/local/components/hpl.signature/themes/js/app.js"></script>-->
<style>
    #workarea-content {
        background-color: #eef2f4 !important;
    }
    canvas{
        max-width: 100%!important;
    }
</style>
