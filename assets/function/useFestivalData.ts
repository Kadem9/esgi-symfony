import { useEffect, useState } from 'react';

const UNSPLASH_API_URL = 'https://api.unsplash.com/photos/random';
const UNSPLASH_ACCESS_KEY = 'hyDTQ5iZ22N7nnGrNdK72dXkXhxyJsuLmoH0RpPDSB8';
const FESTIVAL_API_URL =
    'https://data.culture.gouv.fr/api/explore/v2.1/catalog/datasets/festivals-global-festivals-_-pl/records?select=nom_du_festival%2Cdiscipline_dominante%2Csite_internet_du_festival%2Ccommune_principale_de_deroulement&limit=20';

export function useFestivalData() {
    const [festivals, setFestivals] = useState([]);
    const [images, setImages] = useState<string[]>([]);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        const cachedFestivals = localStorage.getItem('festivals');
        const cachedImages = localStorage.getItem('festivalImages');

        if (cachedFestivals && cachedImages) {
            setFestivals(JSON.parse(cachedFestivals));
            setImages(JSON.parse(cachedImages));
            setLoading(false);
        } else {
            fetchFestivals();
        }
    }, []);

    const fetchFestivals = () => {
        fetch(FESTIVAL_API_URL)
            .then((response) => response.json())
            .then((data) => {
                if (data.results) {
                    setFestivals(data.results);
                    localStorage.setItem('festivals', JSON.stringify(data.results));
                    fetchUnsplashImages(data.results.length);
                } else {
                    console.error('Données inattendues', data);
                    setLoading(false);
                }
            })
            .catch((error) => {
                console.error('Erreur lors du chargement des festivals:', error);
                setLoading(false);
            });
    };

    const fetchUnsplashImages = (count: number) => {
        const cachedImages = localStorage.getItem('festivalImages');

        if (cachedImages) {
            setImages(JSON.parse(cachedImages));
            setLoading(false);
            return;
        }

        const promises = Array.from({ length: count }).map(() =>
            fetch(`${UNSPLASH_API_URL}?query=festival&client_id=${UNSPLASH_ACCESS_KEY}`)
                .then((response) => response.json())
                .then((data) => data.urls.regular)
                .catch((error) => {
                    console.error('Erreur lors du chargement d\'une image Unsplash:', error);
                    return 'https://via.placeholder.com/400x200?text=Image+indisponible';
                })
        );

        Promise.all(promises).then((fetchedImages) => {
            setImages(fetchedImages);
            localStorage.setItem('festivalImages', JSON.stringify(fetchedImages));
            setLoading(false);
        });
    };

    return { festivals, images, loading };
}
