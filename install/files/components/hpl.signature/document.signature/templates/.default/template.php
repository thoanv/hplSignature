<?php
defined('B_PROLOG_INCLUDED') || die;

use Hpl\Signature\Helper\Helper;

$this->addExternalCss('/local/components/hpl.signature/themes/css/font-awesome.min.css');
$this->addExternalCss('/local/components/hpl.signature/themes/css/bootstrap.min.css');
$this->addExternalCss('/local/components/hpl.signature/themes/css/style.css');
$this->addExternalCss('/local/components/hpl.signature/themes/css/style_signature.css');
//$this->addExternalCss('/local/components/hpl.signature/document.signature/templates/.default/pdfcss/viewer.css');
$this->addExternalJs('/local/components/hpl.signature/themes/js/jquery-3.1.1.min.js');
$this->addExternalJs('/local/components/hpl.signature/themes/js/bootstrap.min.js');
$this->addExternalJs('/local/components/hpl.signature/themes/js/script.js');
//$this->addExternalJs('/local/components/hpl.signature/document.signature/templates/.default/pdfjs/viewer.js');
/** @var CBitrixComponentTemplate $this */
$store = $arResult['STORE'];
$userUpload = Helper::getMember($store['created_by']);
$signer = $arResult['SIGNER'];
$method = $signer['method'] ? $signer['method'] : 1;
$date_current = date('d/m/Y')
?>
<link rel="stylesheet" href="/local/components/hpl.signature/document.signature/templates/.default/pdf/css/viewer.css">
<div class="wrapper template_signatire">
    <div class="sidebar">
        <div class="sidebar-header">
            <a href="javascript:;"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Trình ký</a>
        </div><!-- .sidebar-header -->
        <div class="sidebar-nav">
            <div class="btn-group btn-group-rounded justify-content-center d-flex mt-4 mb-4" role="group">
                <button type="button" class="btn btn-uppercase btn-primary px-4">
                    <img src="/local/components/hpl.signature/themes/images/signature-pen.png" width="50" alt=icon-pen">
                    <p class="txt-xac-nhan-ky">Xác nhận ký</p>
                    <span class="text-capitalize">(Xác thực bằng mã OTP SMS)</span>
                </button>
            </div><!-- .btn-group -->
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
        <div id="outerContainer">

            <div id="sidebarContainer">
                <div id="toolbarSidebar">
                    <div id="toolbarSidebarLeft">
                        <div class="splitToolbarButton toggled">
                            <button id="viewThumbnail" class="toolbarButton toggled" title="Show Thumbnails" tabindex="2" data-l10n-id="thumbs">
                                <span data-l10n-id="thumbs_label">Thumbnails</span>
                            </button>
                            <button id="viewOutline" class="toolbarButton" title="Show Document Outline (double-click to expand/collapse all items)" tabindex="3" data-l10n-id="document_outline">
                                <span data-l10n-id="document_outline_label">Document Outline</span>
                            </button>
                            <button id="viewAttachments" class="toolbarButton" title="Show Attachments" tabindex="4" data-l10n-id="attachments">
                                <span data-l10n-id="attachments_label">Attachments</span>
                            </button>
                            <button id="viewLayers" class="toolbarButton" title="Show Layers (double-click to reset all layers to the default state)" tabindex="5" data-l10n-id="layers">
                                <span data-l10n-id="layers_label">Layers</span>
                            </button>
                        </div>
                    </div>

                    <div id="toolbarSidebarRight">
                        <div id="outlineOptionsContainer" class="hidden">
                            <div class="verticalToolbarSeparator"></div>

                            <button id="currentOutlineItem" class="toolbarButton" disabled="disabled" title="Find Current Outline Item" tabindex="6" data-l10n-id="current_outline_item">
                                <span data-l10n-id="current_outline_item_label">Current Outline Item</span>
                            </button>
                        </div>
                    </div>
                </div>
                <div id="sidebarContent">
                    <div id="thumbnailView">
                    </div>
                    <div id="outlineView" class="hidden">
                    </div>
                    <div id="attachmentsView" class="hidden">
                    </div>
                    <div id="layersView" class="hidden">
                    </div>
                </div>
                <div id="sidebarResizer"></div>
            </div>  <!-- sidebarContainer -->

            <div id="mainContainer">
                <div class="findbar hidden doorHanger" id="findbar">
                    <div id="findbarInputContainer">
                        <input id="findInput" class="toolbarField" title="Find" placeholder="Find in document…" tabindex="91" data-l10n-id="find_input">
                        <div class="splitToolbarButton">
                            <button id="findPrevious" class="toolbarButton findPrevious" title="Find the previous occurrence of the phrase" tabindex="92" data-l10n-id="find_previous">
                                <span data-l10n-id="find_previous_label">Previous</span>
                            </button>
                            <div class="splitToolbarButtonSeparator"></div>
                            <button id="findNext" class="toolbarButton findNext" title="Find the next occurrence of the phrase" tabindex="93" data-l10n-id="find_next">
                                <span data-l10n-id="find_next_label">Next</span>
                            </button>
                        </div>
                    </div>

                    <div id="findbarOptionsOneContainer">
                        <input type="checkbox" id="findHighlightAll" class="toolbarField" tabindex="94">
                        <label for="findHighlightAll" class="toolbarLabel" data-l10n-id="find_highlight">Highlight all</label>
                        <input type="checkbox" id="findMatchCase" class="toolbarField" tabindex="95">
                        <label for="findMatchCase" class="toolbarLabel" data-l10n-id="find_match_case_label">Match case</label>
                    </div>
                    <div id="findbarOptionsTwoContainer">
                        <input type="checkbox" id="findEntireWord" class="toolbarField" tabindex="96">
                        <label for="findEntireWord" class="toolbarLabel" data-l10n-id="find_entire_word_label">Whole words</label>
                        <span id="findResultsCount" class="toolbarLabel hidden"></span>
                    </div>

                    <div id="findbarMessageContainer">
                        <span id="findMsg" class="toolbarLabel"></span>
                    </div>
                </div>  <!-- findbar -->

                <div id="secondaryToolbar" class="secondaryToolbar hidden doorHangerRight">
                    <div id="secondaryToolbarButtonContainer">
                        <button id="secondaryPresentationMode" class="secondaryToolbarButton presentationMode visibleLargeView" title="Switch to Presentation Mode" tabindex="51" data-l10n-id="presentation_mode">
                            <span data-l10n-id="presentation_mode_label">Presentation Mode</span>
                        </button>

                        <button id="secondaryOpenFile" class="secondaryToolbarButton openFile visibleLargeView" title="Open File" tabindex="52" data-l10n-id="open_file">
                            <span data-l10n-id="open_file_label">Open</span>
                        </button>

                        <button id="secondaryPrint" class="secondaryToolbarButton print visibleMediumView" title="Print" tabindex="53" data-l10n-id="print">
                            <span data-l10n-id="print_label">Print</span>
                        </button>

                        <button id="secondaryDownload" class="secondaryToolbarButton download visibleMediumView" title="Download" tabindex="54" data-l10n-id="download">
                            <span data-l10n-id="download_label">Download</span>
                        </button>

                        <a href="#" id="secondaryViewBookmark" class="secondaryToolbarButton bookmark visibleSmallView" title="Current view (copy or open in new window)" tabindex="55" data-l10n-id="bookmark">
                            <span data-l10n-id="bookmark_label">Current View</span>
                        </a>

                        <div class="horizontalToolbarSeparator visibleLargeView"></div>

                        <button id="firstPage" class="secondaryToolbarButton firstPage" title="Go to First Page" tabindex="56" data-l10n-id="first_page">
                            <span data-l10n-id="first_page_label">Go to First Page</span>
                        </button>
                        <button id="lastPage" class="secondaryToolbarButton lastPage" title="Go to Last Page" tabindex="57" data-l10n-id="last_page">
                            <span data-l10n-id="last_page_label">Go to Last Page</span>
                        </button>

                        <div class="horizontalToolbarSeparator"></div>

                        <button id="pageRotateCw" class="secondaryToolbarButton rotateCw" title="Rotate Clockwise" tabindex="58" data-l10n-id="page_rotate_cw">
                            <span data-l10n-id="page_rotate_cw_label">Rotate Clockwise</span>
                        </button>
                        <button id="pageRotateCcw" class="secondaryToolbarButton rotateCcw" title="Rotate Counterclockwise" tabindex="59" data-l10n-id="page_rotate_ccw">
                            <span data-l10n-id="page_rotate_ccw_label">Rotate Counterclockwise</span>
                        </button>

                        <div class="horizontalToolbarSeparator"></div>

                        <button id="cursorSelectTool" class="secondaryToolbarButton selectTool toggled" title="Enable Text Selection Tool" tabindex="60" data-l10n-id="cursor_text_select_tool">
                            <span data-l10n-id="cursor_text_select_tool_label">Text Selection Tool</span>
                        </button>
                        <button id="cursorHandTool" class="secondaryToolbarButton handTool" title="Enable Hand Tool" tabindex="61" data-l10n-id="cursor_hand_tool">
                            <span data-l10n-id="cursor_hand_tool_label">Hand Tool</span>
                        </button>

                        <div class="horizontalToolbarSeparator"></div>

                        <button id="scrollPage" class="secondaryToolbarButton scrollModeButtons scrollPage" title="Use Page Scrolling" tabindex="62" data-l10n-id="scroll_page">
                            <span data-l10n-id="scroll_page_label">Page Scrolling</span>
                        </button>
                        <button id="scrollVertical" class="secondaryToolbarButton scrollModeButtons scrollVertical toggled" title="Use Vertical Scrolling" tabindex="63" data-l10n-id="scroll_vertical">
                            <span data-l10n-id="scroll_vertical_label">Vertical Scrolling</span>
                        </button>
                        <button id="scrollHorizontal" class="secondaryToolbarButton scrollModeButtons scrollHorizontal" title="Use Horizontal Scrolling" tabindex="64" data-l10n-id="scroll_horizontal">
                            <span data-l10n-id="scroll_horizontal_label">Horizontal Scrolling</span>
                        </button>
                        <button id="scrollWrapped" class="secondaryToolbarButton scrollModeButtons scrollWrapped" title="Use Wrapped Scrolling" tabindex="65" data-l10n-id="scroll_wrapped">
                            <span data-l10n-id="scroll_wrapped_label">Wrapped Scrolling</span>
                        </button>

                        <div class="horizontalToolbarSeparator scrollModeButtons"></div>

                        <button id="spreadNone" class="secondaryToolbarButton spreadModeButtons spreadNone toggled" title="Do not join page spreads" tabindex="66" data-l10n-id="spread_none">
                            <span data-l10n-id="spread_none_label">No Spreads</span>
                        </button>
                        <button id="spreadOdd" class="secondaryToolbarButton spreadModeButtons spreadOdd" title="Join page spreads starting with odd-numbered pages" tabindex="67" data-l10n-id="spread_odd">
                            <span data-l10n-id="spread_odd_label">Odd Spreads</span>
                        </button>
                        <button id="spreadEven" class="secondaryToolbarButton spreadModeButtons spreadEven" title="Join page spreads starting with even-numbered pages" tabindex="68" data-l10n-id="spread_even">
                            <span data-l10n-id="spread_even_label">Even Spreads</span>
                        </button>

                        <div class="horizontalToolbarSeparator spreadModeButtons"></div>

                        <button id="documentProperties" class="secondaryToolbarButton documentProperties" title="Document Properties…" tabindex="69" data-l10n-id="document_properties">
                            <span data-l10n-id="document_properties_label">Document Properties…</span>
                        </button>
                    </div>
                </div>  <!-- secondaryToolbar -->

                <div class="toolbar">
                    <div id="toolbarContainer">
                        <div id="toolbarViewer">
                            <div id="toolbarViewerLeft">
                                <button id="sidebarToggle" class="toolbarButton" title="Toggle Sidebar" tabindex="11" data-l10n-id="toggle_sidebar" aria-expanded="false" aria-controls="sidebarContainer">
                                    <span data-l10n-id="toggle_sidebar_label">Toggle Sidebar</span>
                                </button>
                                <div class="toolbarButtonSpacer"></div>
                                <button id="viewFind" class="toolbarButton" title="Find in Document" tabindex="12" data-l10n-id="findbar" aria-expanded="false" aria-controls="findbar">
                                    <span data-l10n-id="findbar_label">Find</span>
                                </button>
                                <div class="splitToolbarButton hiddenSmallView">
                                    <button class="toolbarButton pageUp" title="Previous Page" id="previous" tabindex="13" data-l10n-id="previous">
                                        <span data-l10n-id="previous_label">Previous</span>
                                    </button>
                                    <div class="splitToolbarButtonSeparator"></div>
                                    <button class="toolbarButton pageDown" title="Next Page" id="next" tabindex="14" data-l10n-id="next">
                                        <span data-l10n-id="next_label">Next</span>
                                    </button>
                                </div>
                                <input type="number" id="pageNumber" class="toolbarField pageNumber" title="Page" value="1" size="4" min="1" tabindex="15" data-l10n-id="page" autocomplete="off">
                                <span id="numPages" class="toolbarLabel"></span>
                            </div>
                            <div id="toolbarViewerRight">
                                <button id="presentationMode" class="toolbarButton presentationMode hiddenLargeView" title="Switch to Presentation Mode" tabindex="31" data-l10n-id="presentation_mode">
                                    <span data-l10n-id="presentation_mode_label">Presentation Mode</span>
                                </button>

                                <button id="openFile" class="toolbarButton openFile hiddenLargeView" title="Open File" tabindex="32" data-l10n-id="open_file">
                                    <span data-l10n-id="open_file_label">Open</span>
                                </button>

                                <button id="print" class="toolbarButton print hiddenMediumView" title="Print" tabindex="33" data-l10n-id="print">
                                    <span data-l10n-id="print_label">Print</span>
                                </button>

                                <button id="download" class="toolbarButton download hiddenMediumView" title="Download" tabindex="34" data-l10n-id="download">
                                    <span data-l10n-id="download_label">Download</span>
                                </button>
                                <a href="#" id="viewBookmark" class="toolbarButton bookmark hiddenSmallView" title="Current view (copy or open in new window)" tabindex="35" data-l10n-id="bookmark">
                                    <span data-l10n-id="bookmark_label">Current View</span>
                                </a>

                                <!--                                <div class="verticalToolbarSeparator hiddenSmallView"></div>-->

                                <!--                                <button id="secondaryToolbarToggle" class="toolbarButton" title="Tools" tabindex="36" data-l10n-id="tools" aria-expanded="false" aria-controls="secondaryToolbar">-->
                                <!--                                    <span data-l10n-id="tools_label">Tools</span>-->
                                <!--                                </button>-->
                            </div>
                            <div id="toolbarViewerMiddle">
                                <div class="splitToolbarButton">
                                    <button id="zoomOut" class="toolbarButton zoomOut" title="Zoom Out" tabindex="21" data-l10n-id="zoom_out">
                                        <span data-l10n-id="zoom_out_label">Zoom Out</span>
                                    </button>
                                    <div class="splitToolbarButtonSeparator"></div>
                                    <button id="zoomIn" class="toolbarButton zoomIn" title="Zoom In" tabindex="22" data-l10n-id="zoom_in">
                                        <span data-l10n-id="zoom_in_label">Zoom In</span>
                                    </button>
                                </div>
                                <span id="scaleSelectContainer" class="dropdownToolbarButton">
                  <select id="scaleSelect" title="Zoom" tabindex="23" data-l10n-id="zoom">
                    <option id="pageAutoOption" title="" value="auto" selected="selected" data-l10n-id="page_scale_auto">Automatic Zoom</option>
                    <option id="pageActualOption" title="" value="page-actual" data-l10n-id="page_scale_actual">Actual Size</option>
                    <option id="pageFitOption" title="" value="page-fit" data-l10n-id="page_scale_fit">Page Fit</option>
                    <option id="pageWidthOption" title="" value="page-width" data-l10n-id="page_scale_width">Page Width</option>
                    <option id="customScaleOption" title="" value="custom" disabled="disabled" hidden="true"></option>
                    <option title="" value="0.5" data-l10n-id="page_scale_percent" data-l10n-args='{ "scale": 50 }'>50%</option>
                    <option title="" value="0.75" data-l10n-id="page_scale_percent" data-l10n-args='{ "scale": 75 }'>75%</option>
                    <option title="" value="1" data-l10n-id="page_scale_percent" data-l10n-args='{ "scale": 100 }'>100%</option>
                    <option title="" value="1.25" data-l10n-id="page_scale_percent" data-l10n-args='{ "scale": 125 }'>125%</option>
                    <option title="" value="1.5" data-l10n-id="page_scale_percent" data-l10n-args='{ "scale": 150 }'>150%</option>
                    <option title="" value="2" data-l10n-id="page_scale_percent" data-l10n-args='{ "scale": 200 }'>200%</option>
                    <option title="" value="3" data-l10n-id="page_scale_percent" data-l10n-args='{ "scale": 300 }'>300%</option>
                    <option title="" value="4" data-l10n-id="page_scale_percent" data-l10n-args='{ "scale": 400 }'>400%</option>
                  </select>
                </span>
                            </div>
                        </div>
                        <div id="loadingBar">
                            <div class="progress">
                                <div class="glimmer">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="viewerContainer" tabindex="0">
                    <div id="viewer" class="pdfViewer"></div>
                </div>

                <!--#if !MOZCENTRAL-->
                <div id="errorWrapper" hidden='true'>
                    <div id="errorMessageLeft">
                        <span id="errorMessage"></span>
                        <button id="errorShowMore" data-l10n-id="error_more_info">
                            More Information
                        </button>
                        <button id="errorShowLess" data-l10n-id="error_less_info" hidden='true'>
                            Less Information
                        </button>
                    </div>
                    <div id="errorMessageRight">
                        <button id="errorClose" data-l10n-id="error_close">
                            Close
                        </button>
                    </div>
                    <div class="clearBoth"></div>
                    <textarea id="errorMoreInfo" hidden='true' readonly="readonly"></textarea>
                </div>
                <!--#endif-->
            </div> <!-- mainContainer -->

            <div id="overlayContainer" class="hidden">
                <div id="passwordOverlay" class="container hidden">
                    <div class="dialog">
                        <div class="row">
                            <p id="passwordText" data-l10n-id="password_label">Enter the password to open this PDF file:</p>
                        </div>
                        <div class="row">
                            <input type="password" id="password" class="toolbarField">
                        </div>
                        <div class="buttonRow">
                            <button id="passwordCancel" class="overlayButton"><span data-l10n-id="password_cancel">Cancel</span></button>
                            <button id="passwordSubmit" class="overlayButton"><span data-l10n-id="password_ok">OK</span></button>
                        </div>
                    </div>
                </div>
                <div id="documentPropertiesOverlay" class="container hidden">
                    <div class="dialog">
                        <div class="row">
                            <span data-l10n-id="document_properties_file_name">File name:</span> <p id="fileNameField">-</p>
                        </div>
                        <div class="row">
                            <span data-l10n-id="document_properties_file_size">File size:</span> <p id="fileSizeField">-</p>
                        </div>
                        <div class="separator"></div>
                        <div class="row">
                            <span data-l10n-id="document_properties_title">Title:</span> <p id="titleField">-</p>
                        </div>
                        <div class="row">
                            <span data-l10n-id="document_properties_author">Author:</span> <p id="authorField">-</p>
                        </div>
                        <div class="row">
                            <span data-l10n-id="document_properties_subject">Subject:</span> <p id="subjectField">-</p>
                        </div>
                        <div class="row">
                            <span data-l10n-id="document_properties_keywords">Keywords:</span> <p id="keywordsField">-</p>
                        </div>
                        <div class="row">
                            <span data-l10n-id="document_properties_creation_date">Creation Date:</span> <p id="creationDateField">-</p>
                        </div>
                        <div class="row">
                            <span data-l10n-id="document_properties_modification_date">Modification Date:</span> <p id="modificationDateField">-</p>
                        </div>
                        <div class="row">
                            <span data-l10n-id="document_properties_creator">Creator:</span> <p id="creatorField">-</p>
                        </div>
                        <div class="separator"></div>
                        <div class="row">
                            <span data-l10n-id="document_properties_producer">PDF Producer:</span> <p id="producerField">-</p>
                        </div>
                        <div class="row">
                            <span data-l10n-id="document_properties_version">PDF Version:</span> <p id="versionField">-</p>
                        </div>
                        <div class="row">
                            <span data-l10n-id="document_properties_page_count">Page Count:</span> <p id="pageCountField">-</p>
                        </div>
                        <div class="row">
                            <span data-l10n-id="document_properties_page_size">Page Size:</span> <p id="pageSizeField">-</p>
                        </div>
                        <div class="separator"></div>
                        <div class="row">
                            <span data-l10n-id="document_properties_linearized">Fast Web View:</span> <p id="linearizedField">-</p>
                        </div>
                        <div class="buttonRow">
                            <button id="documentPropertiesClose" class="overlayButton"><span data-l10n-id="document_properties_close">Close</span></button>
                        </div>
                    </div>
                </div>
                <!--#if !MOZCENTRAL-->
                <div id="printServiceOverlay" class="container hidden">
                    <div class="dialog">
                        <div class="row">
                            <span data-l10n-id="print_progress_message">Preparing document for printing…</span>
                        </div>
                        <div class="row">
                            <progress value="0" max="100"></progress>
                            <span data-l10n-id="print_progress_percent" data-l10n-args='{ "progress": 0 }' class="relative-progress">0%</span>
                        </div>
                        <div class="buttonRow">
                            <button id="printCancel" class="overlayButton"><span data-l10n-id="print_progress_close">Cancel</span></button>
                        </div>
                    </div>
                </div>
                <!--#endif-->
                <!--#if CHROME-->
                <!--#include viewer-snippet-chrome-overlays.html-->
                <!--#endif-->
            </div>  <!-- overlayContainer -->

        </div> <!-- outerContainer -->
        <div id="printContainer"></div>
    </div><!-- .content -->
    <input type="text" name="heightValue" id="heightValue" value="71" readonly="readonly">
    <input type="text" name="widthValue" id="widthValue" value="150" readonly="readonly">

</div><!-- .wrapper -->
<script defer src="node_modules/es-module-shims/dist/es-module-shims.js"></script>
<script type="importmap-shim">
      {
        "imports": {
          "pdfjs/": "../src/",
          "pdfjs-lib": "../src/pdf.js",
          "pdfjs-web/": "./"
        }
      }
</script>
<script src="/local/components/hpl.signature/document.signature/templates/.default/pdf/js/viewer.js"></script>
<!--<script src="/local/components/hpl.signature/themes/js/pdfjs/pdf.js"></script>-->
<!--<script src="/local/components/hpl.signature/themes/js/interact/interact.min.js"></script>-->
<!--<script src="https://unpkg.com/ionicons@4.4.2/dist/ionicons.js"></script>-->
<!---->
<!--<script>-->
<!--    'use strict';-->
<!---->
<!--    function renderPDF (url, canvasContainer, options) {-->
<!--        var pdfjs = window['pdfjs-dist/build/pdf']-->
<!--        var options = options || { scale: 1 }-->
<!---->
<!--        function renderPage(page) {-->
<!--            var viewport = page.getViewport(options.scale);-->
<!--            var canvas = document.createElement('canvas');-->
<!--            var contex = canvas.getContext('2d');-->
<!--            var renderContext = {-->
<!--                canvasContext: contex,-->
<!--                viewport: viewport-->
<!--            };-->
<!---->
<!--            canvas.height = viewport.height;-->
<!--            canvas.width = viewport.width;-->
<!--            canvasContainer.appendChild(canvas);-->
<!---->
<!--            page.render(renderContext);-->
<!--        }-->
<!---->
<!--        function pageNumber(pdfDoc) {-->
<!--            for(var page = 1; page <= pdfDoc.numPages; page++)-->
<!--                pdfDoc.getPage(page).then(renderPage);-->
<!--            document.getElementById('pageOf').innerHTML += pdfDoc.numPages-->
<!--        }-->
<!---->
<!--        var setPDF = pdfjs.getDocument(url);-->
<!--        setPDF.then(pageNumber)-->
<!--    }-->
<!--    let pdfData = '--><?//=$store['file']?>//';
//
//
//    renderPDF(pdfData, document.getElementById('documentRender'), { scale: 1.55 });
//</script>
//<script src="/local/components/hpl.signature/themes/js/signature.config.js"></script>
//<script>
    //    $(window).scroll(function() {
    //        var scroll = $(window).scrollTop();
    //        if (scroll >= 0.4 * $(window).height()) {
    //            $('.template_signatire .sidebar').css({'position':'fixed', 'top': 0})
    //            $('.content-header').css({'position':'fixed', 'top': 0, 'width': '82%'})
    //        } else {
    //            $('.template_signatire .sidebar').css({'position':'absolute', 'top': 'unset'})
    //            $('.content-header').css({'position':'unset', 'top': 'unset', 'width': '100%'})
    //
    //        }
    //    });
    //    function handleChange(src) {
    //        let check = src.value;
    //        if(check == 1){
    //            $('.info-signature').css('display', 'none')
    //        }else{
    //            $('.info-signature').css('display', 'block')
    //
    //        }
    //    }
    //    function savePdf(){
    //        var windowsize = $(window).width();
    //        let p = $('#pageCurrent').text();
    //        let x = $('.digital-signature').attr('data-x');
    //        let y = $('.digital-signature').attr('data-y');
    //        let w = $('#widthValue').val();
    //        let h = $('#heightValue').val();
    //        if(windowsize >= 1598 && windowsize<=1903){
    //            x = Number(x)-160;
    //        }else if(windowsize >= 1425 && windowsize<1598){
    //            x = Number(x)-73;
    //        }else if(windowsize>1903){
    //            x = Number(x)-265;
    //        }
    //
    //        let id = <?//=$store['id']?>//;
    //        let signature_id = <?//=$signer['id']?>//;
    //        if(!id){
    //            alert('Không xác định đối tượng');
    //            window.location.reload();
    //            return;
    //        }
    //        console.log(x);
    //        if(isNaN(x) || x<=-1){
    //            alert('Vui lòng kéo chữ ký vào trong khung tài liệu');
    //            return;
    //        }
    //        $.ajax({
    //            type: 'POST',
    //            url: '/local/components/hpl.signature/document.signature/templates/.default/ajax.php',
    //            data: {
    //                document_id: id,
    //                signature_id: signature_id,
    //                x: Math.round(x),
    //                y: Math.round(y),
    //                w: Math.round(w),
    //                h: Math.round(h),
    //                p: Number(p),
    //                ajax_action: 'savefilePdf'
    //            },
    //            dataType: 'json',
    //            success: function (response) {
    //                console.log(response)
    //                if (response.SUCCESS == true) {
    //                    alert('Copy tiêu chí thành công');
    //                    window.location.reload();
    //
    //                }
    //            }
    //        });
    //    }
    //</script>
<!--<script src="/local/components/hpl.signature/themes/js/app.js"></script>-->
<style>
    #workarea-content {
        background-color: #eef2f4 !important;
    }
</style>
