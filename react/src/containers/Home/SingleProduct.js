import { Avatar, Box, Dialog, DialogContent, ListItem, ListItemAvatar, ListItemText, makeStyles, TextField, Typography } from '@material-ui/core';
import React, { Fragment, useEffect, useState } from 'react';
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

const SingleProduct = ({product, cartItems, auth}) => {
    const classes = useStyles();
    
    const [productPrice, setProductPrice] = useState({long_id: product.long_id, price: product.price});
    const [disabled, setDisabled] = useState(false);
    const [imageBox, setImageBox] = useState(false);

    useEffect(() => {
        setDisabled(false);
        if(product)
        {
            const findQty = cartItems.find(item => item.long_id === product.long_id);
            if(findQty)
            {
                setDisabled(true);
                setProductPrice({...productPrice, long_id: findQty.long_id, price: findQty.price});
            }
            else
            {
                setProductPrice({...productPrice, long_id: product.long_id, price: product.base_price});
                setDisabled(false);
            }
        }        
    }, [cartItems]);

    return (
        <ListItem alignItems="flex-start">
            <ListItemAvatar>                
                {product.image ?
                <>
                <Avatar alt={product.name} variant="rounded" className={classes.large} src={`/uploads/products/${product.image}`} onClick={() => setImageBox(true)} /> 
                <Dialog
                    open={imageBox}
                    onClose={() => setImageBox(false)}
                    aria-labelledby="draggable-dialog-title"
                >                    
                    <DialogContent>
                        <img src={`/uploads/products/${product.image}`} alt={product.name} width="100%" />
                    </DialogContent>
                </Dialog>
                </>
                : 
                <Avatar alt={product.name} variant="rounded" className={classes.large} src={`/uploads/products/${product.image}`}/>}
                
            </ListItemAvatar>
            <ListItemText className={`${classes.product_area} w-50`}
                primary={product.name}
                secondary={                                   
                <Fragment>
                    <Typography
                        component="p"
                        variant="body2"
                    >
                        {product.description}
                    </Typography>
                    <Box width="50%">
                    <TextField
                        name="price"
                        value={productPrice.price}
                        variant="outlined"
                        label="Price"
                        className="my-2"
                        placeholder="Price"
                        disabled={disabled}
                        size="small"
                        onChange={(e) => setProductPrice({...productPrice, long_id: product.long_id, price: e.target.value})}
                    />
                    </Box>
                </Fragment>
            }
            />
            <ListItemText className={`${classes.product_area} text-right`}
                primary=""
                secondary={                                   
                <Fragment>
                    {product.total_stock > 0 && product.total_stock > product.min_stock_qty && 
                    <Typography
                        component="p"
                        variant="body2"
                        className="pb-2 text-success"
                    >
                       In Stock: {product.total_stock}
                    </Typography>}
                    {product.total_stock > 0 && product.total_stock <= product.min_stock_qty && 
                    <Typography
                        component="p"
                        variant="body2"
                        className="pb-2 text-danger"
                    >
                       In Stock: {product.total_stock}
                    </Typography>}
                    <CartButton product={product} cartItems={cartItems} auth={auth} productPrice={productPrice} />                                        
                </Fragment>
            }
            />
        </ListItem>
    );
}

export default SingleProduct;
