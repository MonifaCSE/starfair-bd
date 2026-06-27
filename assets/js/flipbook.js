/* =========================
   PDF FLIPBOOK
========================= */

pdfjsLib.GlobalWorkerOptions.workerSrc =
"https://cdnjs.cloudflare.com/ajax/libs/pdf.js/4.4.168/pdf.worker.min.js";

const pdfUrl = "assets/pdf/magazine/flipbook.pdf";

const flipContainer = document.getElementById("flipbook");

const flipSound = document.getElementById("flipSound");

flipSound.volume = 0.35;

pdfjsLib.getDocument(pdfUrl).promise.then(async (pdf)=>{

    const pages=[];

    for(let i=1;i<=pdf.numPages;i++){

        const page=await pdf.getPage(i);

        const viewport=page.getViewport({
            scale:2
        });

        const canvas=document.createElement("canvas");

        const ctx=canvas.getContext("2d");

        canvas.width=viewport.width;
        canvas.height=viewport.height;

        await page.render({

            canvasContext:ctx,

            viewport:viewport

        }).promise;

        const div=document.createElement("div");

        div.className="page";

        div.appendChild(canvas);

        pages.push(div);

    }

    const pageFlip=new St.PageFlip(

        flipContainer,

        {

            width:520,

            height:700,

            size:"stretch",

            minWidth:300,

            maxWidth:600,

            minHeight:420,

            maxHeight:760,

            showCover:true,

            usePortrait:true,

            mobileScrollSupport:true,

            maxShadowOpacity:0.6,

            drawShadow:true,

            flippingTime:900,

            startPage:0

        }

    );

    pageFlip.loadFromHTML(pages);

    pageFlip.on("flip",()=>{

        flipSound.currentTime=0;

        flipSound.play();

    });

});