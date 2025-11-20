let pdfDoc = null;
let scale = 1.3;
let pdfContainer = document.getElementById('pdfContainer');
let positions = {};

document.getElementById('pdfUpload').addEventListener('change', e => {
    let file = e.target.files[0];
    if (file && file.type === 'application/pdf') {
        let fileReader = new FileReader();
        fileReader.onload = function() {
            let typedarray = new Uint8Array(this.result);
            pdfjsLib.getDocument(typedarray).promise.then(pdf => {
                pdfDoc = pdf;
                renderAllPages();
            });
        };
        fileReader.readAsArrayBuffer(file);
    }
});

// === Render all pages stacked vertically ===
function renderAllPages() {
    pdfContainer.innerHTML = ''; // clear old pages

    for (let pageNum = 1; pageNum <= pdfDoc.numPages; pageNum++) {
        pdfDoc.getPage(pageNum).then(page => {
            let viewport = page.getViewport({ scale: scale });

            // Page wrapper
            let pageDiv = document.createElement('div');
            pageDiv.className = 'pdf-page';
            pageDiv.style.position = 'relative';
            pageDiv.style.marginBottom = '15px';

            // Canvas for PDF page
            let canvas = document.createElement('canvas');
            canvas.width = viewport.width;
            canvas.height = viewport.height;
            pageDiv.appendChild(canvas);

            // Overlay for placeholders
            let overlay = document.createElement('div');
            overlay.className = 'pdf-overlay';
            overlay.style.position = 'absolute';
            overlay.style.top = '0';
            overlay.style.left = '0';
            overlay.style.width = viewport.width + 'px';
            overlay.style.height = viewport.height + 'px';
            overlay.style.pointerEvents = 'none';
            pageDiv.appendChild(overlay);

            pdfContainer.appendChild(pageDiv);

            let ctx = canvas.getContext('2d');
            page.render({ canvasContext: ctx, viewport: viewport });

            // Save reference for drag/drop
            setupDragAndDrop(canvas, overlay);
        });
    }
}

// === Setup drag/drop for each page ===
function setupDragAndDrop(canvas, overlay) {
    canvas.addEventListener('dragover', e => e.preventDefault());
    canvas.addEventListener('drop', e => {
        e.preventDefault();
        let field = e.dataTransfer.getData('field');
        let rect = canvas.getBoundingClientRect();
        let x = e.clientX - rect.left;
        let y = e.clientY - rect.top;
        createPlaceholder(field, x, y, overlay);
    });
}

// === Create draggable placeholders ===
function createPlaceholder(field, x, y, overlay) {
    let div = document.createElement('div');
    div.className = 'placeholder';
    div.dataset.field = field;
    div.textContent = field;
    div.style.left = x + 'px';
    div.style.top = y + 'px';
    overlay.appendChild(div);
    positions[field] = { x, y };
    makeDraggable(div);
}

// === Make placeholder movable ===
function makeDraggable(el) {
    let offsetX, offsetY, dragging = false;

    el.style.position = 'absolute';
    el.style.background = 'rgba(60,120,255,0.85)';
    el.style.color = '#fff';
    el.style.padding = '3px 8px';
    el.style.borderRadius = '4px';
    el.style.cursor = 'move';
    el.style.pointerEvents = 'auto';
    el.style.fontSize = '12px';

    el.addEventListener('mousedown', e => {
        dragging = true;
        offsetX = e.offsetX;
        offsetY = e.offsetY;
        el.style.zIndex = 1000;
    });

    document.addEventListener('mousemove', e => {
        if (!dragging) return;
        let parentRect = el.parentElement.getBoundingClientRect();
        let x = e.clientX - parentRect.left - offsetX;
        let y = e.clientY - parentRect.top - offsetY;
        el.style.left = x + 'px';
        el.style.top = y + 'px';
    });

    document.addEventListener('mouseup', e => {
        if (dragging) {
            dragging = false;
            el.style.zIndex = '';
            positions[el.dataset.field] = {
                x: parseFloat(el.style.left),
                y: parseFloat(el.style.top)
            };
        }
    });
}

// === Placeholder drag sources (left panel) ===
document.querySelectorAll('.draggable').forEach(el => {
    el.draggable = true;
    el.addEventListener('dragstart', e => {
        e.dataTransfer.setData('field', e.target.dataset.field);
    });
});

// === Generate contracts ===
document.getElementById('generateBtn').addEventListener('click', () => {
    let form = document.getElementById('contractForm');
    let formData = new FormData(form);
    formData.append('positions', JSON.stringify(positions));

    let fileInput = document.getElementById('pdfUpload');
    if (fileInput.files.length > 0) {
        formData.append('template', fileInput.files[0].name);
    }

    fetch('/contract-builder/generate', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(res => {
        alert('Contracts generated successfully!');
    })
    .catch(err => {
        console.error(err);
        alert('Error generating contracts.');
    });
});
