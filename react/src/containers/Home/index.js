import React, { useEffect } from 'react';
import { Col, Container, Row } from 'react-bootstrap';
import Center from './Center';
import LeftSidebar from './leftSidebar';
import SearchBar from './SearchBar';
import { useDispatch } from 'react-redux';
import { getCategories } from '../../actions/categories';

const Home = () => {
    const dispatch = useDispatch();

    useEffect(() => {
        dispatch(getCategories());
    }, [dispatch]);

    return (
        <Container className="mt-5">
            <Row>
                <Col md={{span: 6, offset: 3}}>
                    <SearchBar />
                </Col>
            </Row>
            <Row>
                <Col md={{span: 3}}>
                    <LeftSidebar />
                </Col>
                <Col md={{span: 6}}>
                    <Center />
                </Col>
            </Row>
        </Container>
    )
}

export default Home;