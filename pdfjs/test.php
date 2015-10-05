
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>'Hello, world!' example</title>
</head>
<body>

<ul>
    <li><a href="javascript:goToPage(24);" class="go_to_page" data-page="24">Hungary</a> </li>
</ul>


<canvas id="the-canvas" style="border:1px  solid black"></canvas>

<!-- for legacy browsers add compatibility.js -->
<!--<script src="../compatibility.js"></script>-->

<script src="build/pdf.js"></script>

<script id="script">
    //
    // If absolute URL from the remote server is provided, configure the CORS
    // header on that server.
    //
    var url = 'pdf/test.pdf';
    start_page = 1;
    //
    // Disable workers to avoid yet another cross-origin issue (workers need
    // the URL of the script to be loaded, and dynamically loading a cross-origin
    // script does not work).
    //
    // PDFJS.disableWorker = true;

    //
    // In cases when the pdf.worker.js is located at the different folder than the
    // pdf.js's one, or the pdf.js is executed via eval(), the workerSrc property
    // shall be specified.
    //
    // PDFJS.workerSrc = '../../build/pdf.worker.js';

    //
    // Asynchronous download PDF
    //
    PDFJS.getDocument(url).then(function getPdfHelloWorld(pdf) {
        //
        // Fetch the first page
        //
        pdf.getPage(start_page).then(function getPageHelloWorld(page) {
            var scale = 1.5;
            var viewport = page.getViewport(scale);

            //
            // Prepare canvas using PDF page dimensions
            //
            var canvas = document.getElementById('the-canvas');
            var context = canvas.getContext('2d');
            canvas.height = viewport.height;
            canvas.width = viewport.width;

            //
            // Render PDF page into canvas context
            //
            var renderContext = {
                canvasContext: context,
                viewport: viewport
            };
            page.render(renderContext);
        });



    });

    PDFJS.getDocument(url).then(function goToPage(pdf) {
        //
        // Fetch the first page
        //
        pdf.getPage(1).then(function goToPage(page) {
            pdf.initialBookmark = "page=10";
            pdf.open(url.file);
        });


    });


</script>


</html>
