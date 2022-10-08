import { makeStyles } from '@material-ui/core';
import React from 'react';
import ProductCard from './ProductCard';

const useStyles = makeStyles((theme) => ({
    products: {
        marginTop: '100px',
    }
}));

const Center = ({cartItems, auth, cat_products}) => {
    const classes = useStyles();
    return (
        <ProductCard cartItems={cartItems} auth={auth} className={classes.products} cat_products={cat_products} />
    )
}

export default Center;