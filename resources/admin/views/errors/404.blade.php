<style>
    body {
        overflow: hidden;
    }

    .container {
        width: 100%;
        height: 100vh;
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        background: white;
    }

    .container img {
        width: 500px;
    }

    .container h2 {
        font-family: "Ubuntu", sans-serif;
        text-transform: uppercase;
        text-align: center;
        font-size: 20px;
        font-weight: 600;
        display: block;
        margin: 0;
        color: #1868b7;
        position: relative;
        z-index: 0;
    }

    .container .go-back {
        font-family: "Ubuntu", sans-serif;
        font-size: 16px;
        margin-top: 10px;
    }
    .container a {
        color: #1868b7;
        text-transform: uppercase;
    }
</style>
<div class="container">
    <img src="{{ asset('images/page_not_found.gif') }}" alt="">
    <h2>The page you try to access not found</h2>
    <div class="go-back">
        <a href="{{ url()->previous() }}">Go Back</a>
    </div>
</div>