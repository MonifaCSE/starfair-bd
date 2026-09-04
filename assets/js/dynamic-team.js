document.addEventListener("DOMContentLoaded", () => {
    // 1. Locate all profile card containers
    const mentorCards = document.querySelectorAll('[data-field="mentor-profile"]');
    const advisorCards = document.querySelectorAll('[data-field="advisor-profile"]');
    const trainerCards = document.querySelectorAll('[data-field="trainer-profile"]');

    if (mentorCards.length === 0 && advisorCards.length === 0 && trainerCards.length === 0) {
        return; // No profiles on this page
    }

    // Backup original image / placeholder elements for clean defaults restoration
    const backupCardState = (cards) => {
        cards.forEach(card => {
            const img = card.querySelector('img.member-img');
            const placeholder = card.querySelector('.member-img-placeholder');
            card._originalImg = img ? img.cloneNode(true) : null;
            card._originalPlaceholder = placeholder ? placeholder.cloneNode(true) : null;
        });
    };

    backupCardState(mentorCards);
    backupCardState(advisorCards);
    backupCardState(trainerCards);

    // 2. Fetch dynamic profile data from database (using robust relative paths)
    fetch("backend/get-team.php")
        .then(res => res.json())
        .then(data => {
            if (!data.success) {
                console.warn("Failed to retrieve dynamic profiles:", data.message);
                return;
            }

            const applyProfiles = (cards, list) => {
                cards.forEach((card, index) => {
                    if (index >= list.length) return;
                    const member = list[index];

                    // Update text elements safely preserving other inner elements (e.g. certificates)
                    const h3 = card.querySelector('h3');
                    if (h3) h3.textContent = member.name;

                    const designation = card.querySelector('.designation');
                    if (designation) designation.textContent = member.designation;

                    // Update bio paragraph (checking for text/alignment styles)
                    const p = card.querySelector('p');
                    if (p) p.textContent = member.bio;

                    // Update photo/placeholder
                    let existingImg = card.querySelector('img.member-img');
                    let existingPlaceholder = card.querySelector('.member-img-placeholder');

                    if (member.image_path) {
                        // Custom image uploaded
                        if (existingPlaceholder) {
                            existingPlaceholder.remove();
                        }
                        if (existingImg) {
                            existingImg.src = `${member.image_path}?v=${Date.now()}`;
                            existingImg.alt = member.name;
                        } else {
                            const newImg = document.createElement('img');
                            newImg.src = `${member.image_path}?v=${Date.now()}`;
                            newImg.alt = member.name;
                            newImg.className = 'member-img';
                            card.insertBefore(newImg, card.firstChild);
                        }
                    } else {
                        // Revert to original default state
                        if (existingImg) {
                            existingImg.remove();
                        }
                        if (existingPlaceholder) {
                            existingPlaceholder.remove();
                        }

                        if (card._originalImg) {
                            card.insertBefore(card._originalImg.cloneNode(true), card.firstChild);
                        } else if (card._originalPlaceholder) {
                            card.insertBefore(card._originalPlaceholder.cloneNode(true), card.firstChild);
                        }
                    }
                });
            };

            if (data.mentors) applyProfiles(mentorCards, data.mentors);
            if (data.advisors) applyProfiles(advisorCards, data.advisors);
            if (data.trainers) applyProfiles(trainerCards, data.trainers);
        })
        .catch(err => {
            console.warn("Dynamic profiles backend API not available. Falling back to static assets.", err);
        });
});
