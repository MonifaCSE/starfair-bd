const pdfUrl = "assets/pdf/flipbook.pdf";

let pdfDoc = null;

let currentPage = 1;

let zoom = 1.5;

async function loadPDF() {

    pdfDoc = await pdfjsLib.getDocument(pdfUrl).promise;

    renderSpread();

}

async function renderPage(pageNumber, container) {

    const page = await pdfDoc.getPage(pageNumber);

    const viewport = page.getViewport({

        scale: zoom

    });

    const canvas = document.createElement("canvas");

    const context = canvas.getContext("2d");

    canvas.width = viewport.width;

    canvas.height = viewport.height;

    await page.render({

        canvasContext: context,

        viewport: viewport

    }).promise;

    container.innerHTML = "";

    container.appendChild(canvas);

}

async function renderSpread() {

    const book = document.getElementById("pdf-book");

    book.innerHTML = "";

    const left = document.createElement("div");

    left.className = "page";

    const right = document.createElement("div");

    right.className = "page";

    book.appendChild(left);

    book.appendChild(right);

    await renderPage(currentPage, left);

    if (currentPage + 1 <= pdfDoc.numPages) {

        await renderPage(currentPage + 1, right);

    }
    updateIndicator();
}

loadPDF();


const pages = document.querySelectorAll(".page");

pages.forEach(page => {

    page.animate(

        [

            {

                transform: "scale(.95)",

                opacity: .6

            },

            {

                transform: "scale(1)",

                opacity: 1

            }

        ],

        {

            duration: 350,

            easing: "ease"

        }

    );

});

// Total pages

const totalPages = document.getElementById("totalPages");

const indicator = document.getElementById("pageIndicator");

function updateIndicator() {

    indicator.innerHTML =

        `${currentPage} - ${Math.min(currentPage + 1, pdfDoc.numPages)}`;

    totalPages.innerHTML = ` / ${pdfDoc.numPages}`;

}

// Render

// updateIndicator();


//Next button

document

    .getElementById("nextBtn")

    .addEventListener("click", () => {

        if (currentPage + 1 <= pdfDoc.numPages) {

            currentPage += 1;

            renderSpread();

            playFlip();

        }

    });

// Previous

document

    .getElementById("prevBtn")

    .addEventListener("click", () => {

        if (currentPage > 1) {

            currentPage -= 1;

            renderSpread();

            playFlip();

        }

    });

// Flip sound

const flipSound =

    document.getElementById("flipSound");

function playFlip() {

    flipSound.currentTime = 0;

    flipSound.play();

}


// keyboard

document.addEventListener(

    "keydown",

    e => {

        if (e.key === "ArrowRight") {

            document

                .getElementById("nextBtn")

                .click();

        }

        if (e.key === "ArrowLeft") {

            document

                .getElementById("prevBtn")

                .click();

        }

    });

// Mobile Swipe

let startX = 0;

const book = document.getElementById("pdf-book");

book.addEventListener(

    "touchstart",

    e => {

        startX = e.touches[0].clientX;

    });

book.addEventListener(

    "touchend",

    e => {

        const endX = e.changedTouches[0].clientX;

        const diff = endX - startX;

        if (diff < -80) {

            document

                .getElementById("nextBtn")

                .click();

        }

        if (diff > 80) {

            document

                .getElementById("prevBtn")

                .click();

        }

    });



