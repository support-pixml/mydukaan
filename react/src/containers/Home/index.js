import React, { useEffect } from 'react';
import { Col, Container, Row } from 'react-bootstrap';
import Center from './Center';
import LeftSidebar from './leftSidebar';
import RightSidebar from './rightSidebar';
import SearchBar from './SearchBar';
import { useDispatch, useSelector } from 'react-redux';
import { getCategories } from '../../actions/categories';
import { Hidden } from '@material-ui/core';

const Home = () => {
    const dispatch = useDispatch();    

    const cartItems = useSelector((state) => state.cart.cartItems);

    useEffect(() => {
        dispatch(getCategories());
    }, []);

    return (
        <Container className="">
            <Row>
                <Col md={{span: 6, offset: 3}}>
                    <SearchBar />
                </Col>
            </Row>
            <Row>
                <Hidden only="xs">
                    <Col md={{span: 3}} style={{borderRight: '1px solid #666'}}>
                        <LeftSidebar />
                    </Col>
                </Hidden>
                <Col md={{span: 6}} xs={{span:12}}>
                    <Center cartItems={cartItems} />
                </Col>
                <Hidden only="xs">
                    <Col md={{span: 3}} style={{borderLeft: '1px solid #666'}}>
                        <RightSidebar />
                    </Col>
                </Hidden>
            </Row>
        </Container>
    )
}

export default Home;