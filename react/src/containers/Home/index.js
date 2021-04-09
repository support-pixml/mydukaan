import React, { useEffect } from 'react';
import { Col, Container, Row } from 'react-bootstrap';
import Center from './Center';
import LeftSidebar from './leftSidebar';
import RightSidebar from './rightSidebar';
import SearchBar from './SearchBar';
import { useDispatch } from 'react-redux';
import { getCategories } from '../../actions/categories';
import { Hidden } from '@material-ui/core';

const Home = () => {
    const dispatch = useDispatch();

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
                    <Col md={{span: 3}}>
                        <LeftSidebar />
                    </Col>
                </Hidden>
                <Col md={{span: 6}} xs={{span:12}}>
                    <Center />
                </Col>
                <Hidden only="xs">
                    <Col md={{span: 3}}>
                        <RightSidebar />
                    </Col>
                </Hidden>
            </Row>
        </Container>
    )
}

export default Home;