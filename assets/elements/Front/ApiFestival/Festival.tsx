import { Swiper, SwiperSlide } from 'swiper/react';
import 'swiper/swiper-bundle.css';
// @ts-ignore
import SwiperCore, { Navigation, Pagination } from 'swiper';
import { FestivalCard } from './FestivalCard';
import './style.css';
import {useFestivalData} from "../../../function/useFestivalData.ts";

SwiperCore.use([Navigation, Pagination]);

export function Festival() {
    const { festivals, images, loading } = useFestivalData();

    if (loading) {
        return <div>Chargement des festivals...</div>;
    }

    if (festivals.length === 0) {
        return <div>Aucun festival trouvé.</div>;
    }

    return (
        <div>
            <h2 className="h2 mb-4">Festivals en France</h2>
            <Swiper
                spaceBetween={30}
                slidesPerView={3}
                navigation
                pagination={{ clickable: true }}
                breakpoints={{
                    640: { slidesPerView: 1 },
                    768: { slidesPerView: 2 },
                    1024: { slidesPerView: 3 },
                }}
            >
                {festivals.map((festival: any, index: number) => (
                    <SwiperSlide key={index}>
                        <FestivalCard
                            festival={festival}
                            image={images[index]}
                        />
                    </SwiperSlide>
                ))}
            </Swiper>
        </div>
    );
}
