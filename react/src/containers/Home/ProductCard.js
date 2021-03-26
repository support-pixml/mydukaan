import { Avatar, Badge, Divider, List, ListItem, ListItemAvatar, ListItemText, makeStyles, Typography } from '@material-ui/core';
import React, { Fragment, useEffect } from 'react';
import { useDispatch, useSelector } from 'react-redux';
import { getAllProducts } from '../../actions/products';

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
})); 

const ProductCard = () => {
    const classes = useStyles();
    const dispatch = useDispatch();

    useEffect(() => {
        dispatch(getAllProducts());
    }, [dispatch]);

    const cat_products = useSelector((state) => state.products);

    return (
        <div className={classes.root}>
            {cat_products.map(({long_id, name, products}, index) => {
                return (
                <div key={long_id}>
                    <Typography variant="h6" className={classes.title}>
                        {name} <Badge badgeContent={33} color="primary" className="ml-3" />
                    </Typography>
                    <List className={classes.root}>
                    {/* {products.map((product) => {
                        return (
                            <ListItem alignItems="flex-start" key={product.long_id}>
                                <ListItemAvatar>
                                    <Avatar alt="Remy Sharp" src="/static/images/avatar/1.jpg" />
                                </ListItemAvatar>
                                <ListItemText
                                    primary="Brunch this weekend?"
                                    secondary={
                                    <Fragment>
                                    <Typography
                                        component="span"
                                        variant="body2"
                                        className={classes.inline}
                                        color="textPrimary"
                                    >
                                        Ali Connors
                                    </Typography>
                                    {" — I'll be in your neighborhood doing errands this…"}
                                    </Fragment>
                                }
                                />
                            </ListItem>
                        )})} */}
                    </List>
                </div>
            )})}
            
            {/* <ListGroup as="ul">
                <ListGroup.Item as="li">
                    <Row>
                        <Col md={{span:3}} xs={{span:3}}>
                            <Image src={sample} rounded width="100%" />
                        </Col>
                        <Col md={{span:9}} xs={{span:9}}>
                            <h5>Product Name</h5>
                            <p>Per Piece</p>
                            <div>
                                <span className="font-weight-bold float-left">&#8377; 1300</span>
                                <Button variant="outline-primary float-right" size="sm">Add <FaPlus size="10px" style={{marginTop:'-3px' }} /></Button>
                            </div>
                        </Col>
                    </Row>
                </ListGroup.Item>           
                <ListGroup.Item as="li">
                    <Row>
                        <Col md={{span:3}} xs={{span:3}}>
                            <Image src={sample} rounded width="100%" />
                        </Col>
                        <Col md={{span:9}} xs={{span:9}}>
                            <h5>Product Name</h5>
                            <p>Per Piece</p>
                            <div>
                                <span className="font-weight-bold float-left">&#8377; 1300</span>
                                <Button variant="outline-primary float-right" size="sm">Add <FaPlus size="10px" style={{marginTop:'-3px' }} /></Button>
                            </div>
                        </Col>
                    </Row>
                </ListGroup.Item>           
                <ListGroup.Item as="li">
                    <Row>
                        <Col md={{span:3}} xs={{span:3}}>
                            <Image src={sample} rounded width="100%" />
                        </Col>
                        <Col md={{span:9}} xs={{span:9}}>
                            <h5>Product Name</h5>
                            <p>Per Piece</p>
                            <div>
                                <span className="font-weight-bold float-left">&#8377; 1300</span>
                                <Button variant="outline-primary float-right" size="sm">Add <FaPlus size="10px" style={{marginTop:'-3px' }} /></Button>
                            </div>
                        </Col>
                    </Row>
                </ListGroup.Item>           
            </ListGroup> */}
        </div>
    )
}

export default ProductCard;