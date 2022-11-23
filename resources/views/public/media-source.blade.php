<div class="py-4">
	<h4 class="text-2xl font-bold mb-4">Jellyfin Settings</h4>

	<form action="/dmp-jellyfin/settings" method="post">
		@csrf
		@method('put')
		<div class="mb-5">
			<label for="jellyfin-server-url" class="block mb-2 font-bold"
				>Jellyfin IP Address</label
			>
			<input
				type="text"
				class="w-full mb-2"
				id="jellyfin-server-url"
				aria-describedby="jellyfin-server-urlHelp"
				name="jellyfin_ip_address"
				value="{{ $options['jellyfin_ip_address'] }}"
				required
			/>
			<div id="jellyfin-server-urlHelp" class="text-gray-400 text-sm">Ex: localhost, 10.0.0.32</div>
		</div>

		<div class="mb-5">
			<label for="jellyfin-server-port" class="block mb-2 font-bold"
				>Jellyfin API Token</label
			>
			<input
				type="text"
				class="w-full mb-2"
				id="jellyfin-server-port"
				aria-describedby="jellyfin-server-portHelp"
				name="jellyfin_token"
				value="{{ $options['jellyfin_token'] }}"
				required
			/>
			<div id="jellyfin-server-portHelp" class="text-gray-400 text-sm"></div>
		</div>

		<div class="mb-5">
			<label for="jellyfin-use-ssl" class="text-gray-300 block mb-2 font-bold flex items-center">
				<input
					type="checkbox"
					class="text-black"
					id="jellyfin-use-ssl"
					aria-describedby="plex-use-sslHelp"
					name="jellyfin_use_ssl"
					value="1"
					@checked(old('jellyfin_use_ssl', $options['jellyfin_use_ssl']))
				/>
				<span class="ml-2">Use SSL for Jellyfin IP address</span>
			</label>
			<div id="plex-use-sslHelp" class="text-gray-400 text-sm"></div>
		</div>

		<button type="submit" class="btn text-black bg-gray-300 text-md px-3 py-1 rounded-sm hover:bg-gray-100">Save Jellyfin Settings</button>
	</form>
</div>
