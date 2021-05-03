import { Avatar, Backdrop, Badge, Button, Divider, Fade, Grid, List, ListItem, ListItemAvatar, ListItemText, makeStyles, Modal, Typography } from '@material-ui/core';
import React, { Fragment, useEffect, useState } from 'react';
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
})); 

const ProductCard = ({cartItems}) => {
    const classes = useStyles();
    const dispatch = useDispatch();
    const [openOption, setOpenOption] = useState(false);

    useEffect(() => {
        dispatch(getAllProducts());
    }, [dispatch]);

    const handleCloseModal = () => {
        setOpenOption(false);
    };

    const handleOpenModal = () => {
        setOpenOption(true);
    };

    const cat_products = useSelector((state) => state.product.products);  

    return (
        <div className={classes.root}>
            {cat_products.map(({long_id, name, products, slug, product_count}) => {
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
                                        {product.stock > 0 && product.product_options.length == 0 ?
                                        <CartButton product={product} cartItems={cartItems} />
                                        :
                                        <div className="float-right">
                                            <Button size="small" color="secondary" variant="outlined" onClick={handleOpenModal}>Select</Button>
                                            <Modal
                                                className={classes.modal}
                                                open={openOption}
                                                onClose={handleCloseModal}
                                                closeAfterTransition
                                                BackdropComponent={Backdrop}
                                                BackdropProps={{
                                                    timeout: 500,
                                                }}
                                            >
                                                <Fade in={openOption}>
                                                    <div className={classes.paper}>
                                                        <Typography component="h1" variant="h5">
                                                            Select Option
                                                        </Typography>
                                                        <Grid container spacing={2}>   
                                                            {product.product_options.map((option, index) => {
                                                                return (
                                                                <Grid item xs={12} md={4} key={index} className="text-center">
                                                                    <Typography component="p" variant="body1">
                                                                        {option.option_name}
                                                                    </Typography>
                                                                    <Typography component="p" variant="body2">
                                                                        Stock: {option.option_stock}
                                                                    </Typography>
                                                                    <Typography component="p" variant="caption">
                                                                        &#8377; {option.option_price}
                                                                    </Typography>
                                                                    <CartButton option={option} cartItems={cartItems} />
                                                                </Grid>
                                                                );
                                                            })}
                                                        </Grid>
                                                    </div>
                                                </Fade>
                                            </Modal>
                                        </div>
                                        }
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