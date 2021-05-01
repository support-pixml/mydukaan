import React from 'react';
import ProductCard from './ProductCard';

const Center = ({cartItems}) => {
    return (
        <ProductCard cartItems={cartItems} />
    )
}

export default Center;