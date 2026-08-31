
    export const getElementWithInlineStyles = function(el) {
      const clone = el.cloneNode(true);
      const origElements = [el, ...el.querySelectorAll('*')];
      const cloneElements = [clone, ...clone.querySelectorAll('*')];

      origElements.forEach((origEl, index) => {
        const computedStyle = window.getComputedStyle(origEl);
        let styleString = '';
        
        // Salin semua properti CSS yang aktif ke style inline
        for (let i = 0; i < computedStyle.length; i++) {
          const prop = computedStyle[i];
          styleString += `${prop}:${computedStyle.getPropertyValue(prop)};`;
        }
        
        cloneElements[index].setAttribute('style', styleString);
      });

      return clone;
    }

    export const downloadHtmlAsWordById = function(elementId, filename = 'download') {
      const targetElement = document.getElementById(elementId);
      if (!targetElement) {
        console.error(`Print Error: Element with ID "${elementId}" not found.`);
        return;
      }
      
      let htmlBody = getElementWithInlineStyles(targetElement)
      htmlBody = htmlBody.outerHTML
      // 1. Definisikan CSS dan Meta secara terpisah agar rapi
            // 2. Header HTML (Tag <html> dan <head> TIDAK BOLEH ditutup sebelum body)
      const htmlHeader = `
        <html xmlns:o='urn:schemas-microsoft-com:office:office' 
              xmlns:w='urn:schemas-microsoft-com:office:word' 
              xmlns='http://www.w3.org/TR/REC-html40'>
          <head>
            <meta charset='utf-8'>
            <title>${filename}</title>
          </head>
          <body>
      `;

      const htmlFooter = "</body></html>";

      // Gabungkan semua komponen
      const source = htmlHeader + htmlBody + htmlFooter;
      // newTab.document.close();
      // Gunakan Blob dengan BOM (\ufeff) agar karakter khusus/UTF-8 terbaca dengan benar
      const blob = new Blob(['\ufeff', source], { type: 'application/msword' });
      const url = URL.createObjectURL(blob);
      const link = document.createElement('a');
      link.href = url;
      
      // Format nama file
      const fileName = filename + `.doc`;
      link.download = fileName;
      
      document.body.appendChild(link); // Penting untuk beberapa browser
      link.click();
      document.body.removeChild(link);
      
      URL.revokeObjectURL(url);
    }


  export const printElementById = (elementId, options = {}) => {
      const targetElement = document.getElementById(elementId);
      if (!targetElement) {
        console.error(`Print Error: Element with ID "${elementId}" not found.`);
        return;
      }

      // 1. Create hidden iframe container
      const iframe = document.createElement('iframe');
      iframe.style.position = 'fixed';
      iframe.style.right = '0';
      iframe.style.bottom = '0';
      iframe.style.width = '0';
      iframe.style.height = '0';
      iframe.style.border = '0';
      iframe.style.visibility = 'hidden';
      document.body.appendChild(iframe);

      const iframeDoc = iframe.contentWindow.document;

      // 2. Clone active Tailwind CSS links and embedded style tags
      const styleNodes = Array.from(
        document.querySelectorAll('link[rel="stylesheet"], style')
      );
      
      const stylesHTML = styleNodes
        .map((node) => node.outerHTML)
        .join('\n');

      // 3. Write target HTML and styles into iframe document
      iframeDoc.open();
      iframeDoc.write(`
        <!DOCTYPE html>
        <html lang="id">
          <head>
            <meta charset="UTF-8" />
            <meta name="viewport" content="width=device-width, initial-scale=1.0" />
            <title>${options.pageTitle || 'Print Document'}</title>
            ${stylesHTML}
            <style>
              @page {
                size: ${options.paperSize || 'A4'};
                margin: ${options.margin || '10mm'};
              }
              body {
                background-color: #ffffff !important;
                color: #000000 !important;
                margin: 0 !important;
                padding: 0 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
              }
            </style>
          </head>
          <body>
            <div class="printable-wrapper">
              ${targetElement.outerHTML}
            </div>
          </body>
        </html>
      `);
      iframeDoc.close();

      // 4. Trigger print after styles load and destroy iframe post-print
      iframe.contentWindow.focus();
      
      // Timeout ensures dynamic font styles and Tailwind assets finish loading
      setTimeout(() => {
        iframe.contentWindow.print();
        document.body.removeChild(iframe);
      }, options.delay || 300);
    }