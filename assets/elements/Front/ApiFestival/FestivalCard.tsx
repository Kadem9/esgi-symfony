import './style.css';

interface FestivalCardProps {
    festival: any;
    image: string;
}

export function FestivalCard({ festival, image }: FestivalCardProps) {
    return (
        <div className="festival-card">
            <img
                src={image || 'https://via.placeholder.com/400x200?text=Chargement'}
                alt={`Illustration du festival ${festival.nom_du_festival}`}
                className="festival-image"
            />
            <div className="festival-content">
                <h2>{festival.nom_du_festival}</h2>
                <p><strong>Discipline:</strong> {festival.discipline_dominante}</p>
                <p><strong>Ville:</strong> {festival.commune_principale_de_deroulement}</p>
                {festival.site_internet_du_festival && (
                    <a
                        href={festival.site_internet_du_festival.startsWith('http')
                            ? festival.site_internet_du_festival
                            : `http://${festival.site_internet_du_festival}`
                        }
                        target="_blank"
                        rel="noopener noreferrer"
                    >
                        Site officiel
                    </a>
                )}
            </div>
        </div>
    );
}
