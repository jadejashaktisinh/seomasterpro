jQuery( document ).ready(
	function ($) {
		$.post(
			seoChartsAjax.ajax_url,
			{ action: 'seo_master_get_chart_data' },
			function (response) {
				// Line chart data
				let dates  = response.scores.map( item => item.date );
				let scores = response.scores.map( item => item.avg_score );

				new Chart(
					document.getElementById( "seoLineChart" ),
					{
						type: 'line',
						data: {
							labels: dates,
							datasets: [{
								label: "Average SEO Score",
								data: scores,
								borderColor: "#36a2eb",
								fill: false
							}]
						},
						options: {
							responsive: false,
							maintainAspectRatio: false
						}
					}
				);

				// Pie chart data
				let types  = response.types.map( item => item.post_type );
				let counts = response.types.map( item => item.count );

				new Chart(
					document.getElementById( "seoPieChart" ),
					{
						type: 'pie',
						data: {
							labels: types,
							datasets: [{
								data: counts,
								backgroundColor: ["#ff6384", "#36a2eb", "#ffcd56", "#4bc0c0"]
							}]
						},
						options: {
							responsive: false,
							maintainAspectRatio: false
						}
					}
				);
			}
		);
	}
);
