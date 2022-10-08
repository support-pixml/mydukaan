import { Badge, CircularProgress, Divider, List, makeStyles, Typography } from '@material-ui/core';
import React, { Fragment } from 'react';
import { useSelector } from 'react-redux';
import SingleProduct from './SingleProduct';

const useStyles = makeStyles((theme) => ({
    root: {
        width: '100%',
        backgroundColor: theme.palette.background.paper,
    },
    inline: {
        display: 'inline',
    },
    title: {
        margin: `${theme.spacing(4)}px 0 ${theme.spacing(2)}px`,
    },
    product_area: {
        fontSize: '16px',
        marginLeft: `${theme.spacing(2)}px`,
    },
    large: {
        width: theme.spacing(10),
        height: theme.spacing(10),
    },
    modal: {
        display: 'flex',
        alignItems: 'center',
        justifyContent: 'center',
    },
    paper: {
        backgroundColor: theme.palette.background.paper,
        border: '2px solid #000',
        boxShadow: theme.shadows[5],
        padding: theme.spacing(2, 4, 3),
        width: '500px'
    },
    loader: {
        position: 'relative',
        marginLeft:'50%',
        top:'50%',
        height:'100vh'
    }
})); 

const ProductCard = ({cartItems, auth, cat_products}) => {
    const classes = useStyles();

    const error = useSelector(state => state.error.error);

    if(cat_products.length === 0)
    {
        if(error)
        {
            return (
                <div>
                    <Typography variant="h6" className={classes.title}>
                        {error}
                    </Typography>
                </div>  
            )
        }

        return (
            <div className={classes.loader}>
                <CircularProgress color="secondary" />
            </div>
        )
    }
    
    return (     
        <div className={classes.root}>
            {cat_products && cat_products.map(({long_id, name, products, slug, product_count}) => {
                return (
                <div key={long_id} id={slug} className="element">
                    <Typography variant="h6" className={classes.title}>
                        {name} <Badge badgeContent={product_count} color="primary" className="ml-3 pb-1" />
                    </Typography>
                    <List className={classes.root}>
                    {products ? products.map((product) => {                        
                        return (
                            <Fragment key={product.long_id}>
                            <SingleProduct product={product} cartItems={cartItems} auth={auth} />
                            <Divider />
                            </Fragment>
                        )}) : 
                            <div></div>                        
                        } 
                    </List>
                </div>
            )})
            }
        </div>
    )
}

export default ProductCard;