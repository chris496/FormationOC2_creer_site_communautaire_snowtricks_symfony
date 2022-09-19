const newComment = document.querySelector('.newComment')
const seeMore = document.querySelector('#seeMore')
const slug = document.getElementById("slug").innerHTML
let num = 2

seeMore.addEventListener('click', (e)=>{
    e.preventDefault()
    const cpt = num++
    //let url = Routing.generate('see_more', {'page' : yep});
    let url = '{{ path("see_more_comment", {'slug' : 'slug', 'page': 'page'}) }}'; 
    url = url.replace("slug", slug);
    url = url.replace("page", cpt);
    fetch((url)).then(
            response => response.json()
        ).then(
            data => {
                data.forEach(el => {
                    console.log(el)
                    let newp = document.createElement('p')
                    newp.innerHTML = `
                        <div class="d-flex align-items-center">
                            <div class="me-2 p-3">
                                <img src="/img/avatar.png" class="avatar card-img-top img-fluid rounded mb-4 mb-lg-0" alt="picture">
                            </div>
                            <div class="mb-2 p-3 border shadow">
                                ${el.content}
                            </div>
                        </div>`
                    newComment.append(newp);
                })
            }
        )
})