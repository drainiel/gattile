// js/GattiApp.jsx
const { useState, useEffect } = React;

function GattiApp() {
    const [gatti, setGatti] = useState([]);
    const [filtroTesto, setFiltroTesto] = useState('');
    const [ordinamento, setOrdinamento] = useState('data_arrivo');
    const [selezionati, setSelezionati] = useState([]);

    useEffect(() => {
        // Fetch dei dati JSON dal backend
        fetch('api/gatti.php')
            .then(response => response.json())
            .then(data => {
                if (Array.isArray(data)) {
                    setGatti(data);
                } else {
                    console.error("Errore fetch gatti:", data);
                }
            })
            .catch(error => console.error("Errore di rete:", error));
    }, []);

    // Dispatch CustomEvent quando cambia la selezione
    useEffect(() => {
        const selectedCatsData = gatti.filter(g => selezionati.includes(g.id));
        const event = new CustomEvent('catsSelected', { detail: selectedCatsData });
        document.dispatchEvent(event);
    }, [selezionati, gatti]);

    const toggleSelezione = (id) => {
        if (!window.IS_LOGGED_IN) return; // Solo gli utenti autenticati possono selezionare

        setSelezionati(prev => {
            if (prev.includes(id)) {
                return prev.filter(catId => catId !== id);
            } else {
                return [...prev, id];
            }
        });
    };

    // Filtra per nome o descrizione
    let gattiFiltrati = gatti.filter(gatto => {
        const testoSorgente = gatto.nome.toLowerCase() + " " + gatto.descrizione.toLowerCase();
        return testoSorgente.includes(filtroTesto.toLowerCase());
    });

    // Ordina i risultati
    gattiFiltrati.sort((a, b) => {
        if (ordinamento === 'eta') {
            return a.eta - b.eta;
        } else if (ordinamento === 'colore_mantello') {
            return a.colore_mantello.localeCompare(b.colore_mantello);
        } else {
            // data_arrivo (descending)
            return new Date(b.data_arrivo) - new Date(a.data_arrivo);
        }
    });

    return (
        <>
            <div className="controls mb-2" style={{ display: 'flex', gap: '20px', flexWrap: 'wrap', padding: '10px 0' }}>
                <div className="form-group" style={{ flex: '1', minWidth: '250px', marginBottom: 0 }}>
                    <label>Ricerca (nome o descrizione):</label>
                    <input
                        type="text"
                        value={filtroTesto}
                        onChange={(e) => setFiltroTesto(e.target.value)}
                        placeholder="Cerca un gatto..."
                    />
                </div>
                <div className="form-group" style={{ flex: '1', minWidth: '250px', marginBottom: 0 }}>
                    <label>Ordina per:</label>
                    <select
                        value={ordinamento}
                        onChange={(e) => setOrdinamento(e.target.value)}
                    >
                        <option value="data_arrivo">Data di arrivo</option>
                        <option value="eta">Età</option>
                        <option value="colore_mantello">Colore mantello</option>
                    </select>
                </div>
            </div>

            {gattiFiltrati.length === 0 ? (
                <p>Nessun gatto trovato con questi criteri.</p>
            ) : (
                <ul className="cat-gallery">
                    {gattiFiltrati.map(gatto => (
                        <li key={gatto.id}>
                            <article
                                className={`cat-card ${selezionati.includes(gatto.id) ? 'selected' : ''}`}
                                onClick={() => toggleSelezione(gatto.id)}
                                style={{ cursor: window.IS_LOGGED_IN ? 'pointer' : 'default' }}
                            >
                                <figure className="cat-img" style={{ background: `url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><text y="50" x="50" text-anchor="middle" dominant-baseline="middle" font-size="40">🐈</text></svg>') center/cover`, backgroundColor: '#eee' }}></figure>
                                <div className="cat-info">
                                    <h3>{gatto.nome}</h3>
                                    <p><strong>Età:</strong> {gatto.eta} mesi</p>
                                    <p><strong>Colore:</strong> {gatto.colore_mantello}</p>
                                    <p><strong>Sesso:</strong> {gatto.sesso}</p>
                                    <p><strong>Arrivo:</strong> {gatto.data_arrivo}</p>
                                    <p style={{ fontSize: '0.8rem', marginTop: '10px', color: '#666' }}>{gatto.descrizione}</p>
                                </div>
                            </article>
                        </li>
                    ))}
                </ul>
            )}
        </>
    );
}

const rootNode = document.getElementById('react-root');
if (rootNode) {
    const root = ReactDOM.createRoot(rootNode);
    root.render(<GattiApp />);
}
