const api = async (url, method, body = null) => {
    let params = {
        headers: {
            "Accept": "application/json",
            "Content-Type": "application/json",
        },
        method: method,
    }
    if (body !== null) {
        params.body = JSON.stringify(body);
    }
    const response = await fetch(url, params);
    const status = response.status;
    const data =   await response.json();
    return [status, data];
}


const createErrorMessage = (form,  errors) => {
    for (let [key, value] of Object.entries(errors)) {
        const input = form.querySelector(`.${key}`);

        console.log(input)
        let textError = '';
        value.forEach(error => textError += `<div class="error-message">${error}</div>`)
        input.insertAdjacentHTML('afterend', textError);
    }
}

const sendMessage = async (event, replyId = null) => {
    event.preventDefault();
    const form = event.target;
    const formData = new FormData(form);


    const [status, data] = await api("/api/post", 'POST', {
        name: formData.get('name'),
        message: formData.get('message'),
        reply_id: replyId
    });


    if (status === 201) {
        form.reset();
        return data.data;
    }  else if (status === 422) {
        createErrorMessage(form,  data.errors);
        return null;
    }
}

const loadEventListeners = () => {
    const createForms = document.querySelectorAll('.form__create');

    createForms.forEach(createForm => {
        let replyId = null;
        if (createForm.id.includes('-')) {
            const idParts = createForm.id.split('-');
            replyId = idParts[idParts.length - 1];
        }
        createForm.onsubmit =  async e => {
            const data = await sendMessage(e, replyId);
            const post = data.post;
            let inserted = '';
            if (post) {
                inserted = `<div class='post'>
                                <div class='post__title'>
                                    <span class='post__name'>${post.name}</span>
                                    <span class='post__date'>${post.created_at}</span>
                                </div>
                                <div class='post__message'>${post.message}</div>
                                 <div id="${post.id}" class='post__reply'>
                                            <span>+</span>
                                            <span class='post__reply-dashed'>Ответить</span>
                                  </div>
                                  <div class='post__nested'>
                              </div>`;

                if (replyId) {
                    e.target.parentElement.querySelector('.post__nested').insertAdjacentHTML('beforeend', inserted);
                } else {
                    document.querySelector('.posts').insertAdjacentHTML('beforeend', inserted);
                }
            }

            if (replyId) {
                loadReplyButtonsEventListeners();
                hideForm(e.target.previousSibling)
            }
        }

        createForm.oninput = () => {
            const errorMessages = createForm.querySelectorAll(".error-message");
            errorMessages.forEach(message => message.remove())
        };
    })
}


const showForm = block => {
    const id = block.id;
    block.insertAdjacentHTML(
        'afterend',
        `<form id="create-${id}"class="form form__create"  name="create-${id}" >
            <input id="create-name-${id}" type="text" name="name" class="name input" placeholder="Введите свое имя">
            <textarea id="create-message-${id}" type="text" name="message" class="message textarea" placeholder="Введите сообщение"></textarea>
            <button class="btn" type="submit">Запостить</button>
        </form>`
    );

    block.onclick = () => hideForm(block)
    loadEventListeners();
}

const hideForm = block => {
    block.nextSibling.remove();
    block.onclick = () => showForm(block)
    loadEventListeners();
}

const loadReplyButtonsEventListeners = () => {
    const replyButtons = document.querySelectorAll('.post__reply');
    replyButtons.forEach(btn => btn.onclick = () => showForm(btn))
}


loadEventListeners();
loadReplyButtonsEventListeners();






