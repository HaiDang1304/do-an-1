const locations = [
    {
        name: "Bắc Đảo",
        count: 13,
    },
    {
        name: "Bãi Khem | Cáp Hòn Thơm",
        count: 7,
        subtext: "Địa Trung Hải, Cầu Hôn, Ga An Thới",
    },
    {
        name: "Chợ đêm Dinh Cậu",
        count: 25,
        subtext: "Trung tâm thị trấn",
    },
    {
        name: "Dương Đông",
        count: 25,
        subtext: "Gần khu trung tâm",
    },
    {
        name: "Nam Đảo",
        count: 28,
        subtext: "Gần sân bay",
    },
    {
        name: "Phú Quốc United Center",
        count: 12,
        subtext: "Grand World, VinWonders, Safari",
    }
];

const locationContainer = document.getElementById('location-list');

locations.forEach((location, index) => {
    const id = `location-${index}`;
    const wrapper = document.createElement('div');
    wrapper.className = "form-check mb-2";

    wrapper.innerHTML = `
    <input class="form-check-input" type="checkbox" id="${id}">
    <label class="form-check-label" for="${id}">
      ${location.name} <span class="text-muted">(${location.count})</span>
      ${location.subtext ? `<br><small class="text-muted">${location.subtext}</small>` : ''}
    </label>
  `;

    locationContainer.appendChild(wrapper);
});