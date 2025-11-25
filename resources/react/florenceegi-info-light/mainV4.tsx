/**
 * FlorenceEGI - Pagina Informativa V4
 * Entry point per il mounting React
 */

import '@vitejs/plugin-react/preamble';
import React from 'react';
import { createRoot } from 'react-dom/client';
import InformativePageLightV4 from './InformativePageLightV4';

// Mount React app
const container = document.getElementById('root');
if (container) {
    const root = createRoot(container);
    root.render(
        <React.StrictMode>
            <InformativePageLightV4 />
        </React.StrictMode>
    );
}
