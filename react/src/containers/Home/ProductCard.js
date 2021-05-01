import { Avatar, Badge, Button, Divider, List, ListItem, ListItemAvatar, ListItemText, makeStyles, Typography } from '@material-ui/core';
import React, { Fragment, useEffect } from 'react';
import { useDispatch, useSelector } from 'react-redux';
import { getAllProducts } from '../../actions/products';
import CartButton from '../../components/UI/CartButton';

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
        marginLeft: `${theme.spacing(2)}px`,
    },
    large: {
        width: theme.spacing(10),
        height: theme.spacing(10),
    },
})); 

const ProductCard = ({cartItems}) => {
    const classes = useStyles();
    const dispatch = useDispatch();

    useEffect(() => {
        dispatch(getAllProducts());
    }, [dispatch]);

    const cat_products = useSelector((state) => state.product.products);  

    return (
        <div className={classes.root}>
            {cat_products.map(({long_id, name, products, slug, product_count}, index) => {
                return (
                <div key={long_id} id={slug}>
                    <Typography variant="h6" className={classes.title}>
                        {name} <Badge badgeContent={product_count} color="primary" className="ml-3 pb-1" />
                    </Typography>
                    <List className={classes.root}>
                    {products ? products.map((product) => {
                        return (
                            <Fragment key={product.long_id}>
                            <ListItem alignItems="flex-start">
                                <ListItemAvatar>
                                    <Avatar alt={product.name} variant="rounded" className={classes.large} src={`/uploads/products/${product.image}`} />
                                </ListItemAvatar>
                                <ListItemText className={classes.product_area}
                                    primary={product.name}
                                    secondary={                                   
                                    <Fragment>
                                        <Typography
                                            component="p"
                                            variant="body2"
                                            className={classes.inline}
                                            color="textPrimary"
                                        >
                                            &#8377; {product.price}
                                        </Typography>
                                        <Typography
                                            component="p"
                                            variant="body2"
                                            color="textPrimary"
                                        >
                                            Stock: {product.stock}
                                        </Typography>
                                        <CartButton product={product} cartItems={cartItems} />
                                    </Fragment>
                                }
                                />
                            </ListItem>
                            <Divider />
                            </Fragment>
                        )}) : 
                            <div></div>                        
                        } 
                    </List>
                </div>
            )})}
        </div>
    )
}

export default ProductCard;