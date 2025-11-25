/**
 * FlorenceEGI - Pagina Informativa Light
 * Entry point per il mounting React
 */

import '@vitejs/plugin-react/preamble';
import React from 'react';
import { createRoot } from 'react-dom/client';
import InformativePageLight from './InformativePageLight';

// Mount React app
const container = document.getElementById('root');
if (container) {
    const root = createRoot(container);
    root.render(
        <React.StrictMode>
            <InformativePageLight />
        </React.StrictMode>
    );
}
